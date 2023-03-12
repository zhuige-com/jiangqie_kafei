<?php

/*
 * 酱茄小程序开源版
 * Author: 追格
 * Help document: https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright ️© 2020-2023 www.zhuige.com All rights reserved.
 */


/* PHP远程下载微信头像存到本地,本地图片转base64
 * $url 微信头像链接
 * $path 要保存图片的目录
 * $user_id 用户唯一标识
 */
if (!function_exists('jiangqie_free_download_wx_avatar')) {
    function jiangqie_free_download_wx_avatar($url, $user_id)
    {
        // $header = [
        //     'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0',
        //     'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
        //     'Accept-Encoding: gzip, deflate',
        // ];
        // $curl = curl_init();
        // curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        // curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        // $data = curl_exec($curl);
        // $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // curl_close($curl);

        $response = wp_remote_get($url);
        $http_code = wp_remote_retrieve_response_code($response);
        

        if ($http_code == 200) { //把URL格式的图片转成base64_encode格式的！     
            $data = wp_remote_retrieve_body($response); 
            $imgBase64Code = "data:image/jpeg;base64," . base64_encode($data);
        }
        $img_content = $imgBase64Code; //图片内容  
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img_content, $result)) {
            $type = $result[2]; //得到图片类型png jpg gif

            $upload_dir = wp_upload_dir();
            $filename = 'jiangqie_avatar_' . $user_id . ".{$type}";
            $filepath = $upload_dir['path'] . '/' . $filename;
            if (file_put_contents($filepath, base64_decode(str_replace($result[1], '', $img_content)))) {
                return ['path' => $filepath, 'url' => $upload_dir['url'] . '/' . $filename];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

if (!function_exists('jiangqie_free_import_image2attachment')) {
    //把图片添加到媒体库
    function jiangqie_free_import_image2attachment($file, $post_id = 0, $import_date = 'current')
    {
        set_time_limit(0);

        // Initially, Base it on the -current- time.
        $time = current_time('mysql', 1);
        // Next, If it's post to base the upload off:
        if ('post' == $import_date && $post_id > 0) {
            $post = get_post($post_id);
            if ($post && substr($post->post_date_gmt, 0, 4) > 0) {
                $time = $post->post_date_gmt;
            }
        } elseif ('file' == $import_date) {
            $time = gmdate('Y-m-d H:i:s', @filemtime($file));
        }

        // A writable uploads dir will pass this test. Again, there's no point overriding this one.
        if (!(($uploads = wp_upload_dir($time)) && false === $uploads['error'])) {
            return new WP_Error('upload_error', $uploads['error']);
        }

        $wp_filetype = wp_check_filetype($file, null);

        extract($wp_filetype);

        if ((!$type || !$ext) && !current_user_can('unfiltered_upload')) {
            return new WP_Error('wrong_file_type', __('Sorry, this file type is not permitted for security reasons.', 'add-from-server'));
        }

        // Is the file allready in the uploads folder?
        // WP < 4.4 Compat: ucfirt
        if (preg_match('|^' . preg_quote(ucfirst(wp_normalize_path($uploads['basedir'])), '|') . '(.*)$|i', $file, $mat)) {

            $filename = basename($file);
            $new_file = $file;

            $url = $uploads['baseurl'] . $mat[1];

            $attachment = get_posts(array('post_type' => 'attachment', 'meta_key' => '_wp_attached_file', 'meta_value' => ltrim($mat[1], '/')));
            if (!empty($attachment)) {
                return new WP_Error('file_exists', __('Sorry, That file already exists in the WordPress media library.', 'add-from-server'));
            }

            // Ok, Its in the uploads folder, But NOT in WordPress's media library.
            if ('file' == $import_date) {
                $time = @filemtime($file);
                if (preg_match("|(\d+)/(\d+)|", $mat[1], $datemat)) { // So lets set the date of the import to the date folder its in, IF its in a date folder.
                    $hour = $min = $sec = 0;
                    $day = 1;
                    $year = $datemat[1];
                    $month = $datemat[2];

                    // If the files datetime is set, and it's in the same region of upload directory, set the minute details to that too, else, override it.
                    if ($time && date('Y-m', $time) == "$year-$month") {
                        list($hour, $min, $sec, $day) = explode(';', date('H;i;s;j', $time));
                    }

                    $time = mktime($hour, $min, $sec, $month, $day, $year);
                }
                $time = gmdate('Y-m-d H:i:s', $time);

                // A new time has been found! Get the new uploads folder:
                // A writable uploads dir will pass this test. Again, there's no point overriding this one.
                if (!(($uploads = wp_upload_dir($time)) && false === $uploads['error'])) {
                    return new WP_Error('upload_error', $uploads['error']);
                }
                $url = $uploads['baseurl'] . $mat[1];
            }
        } else {
            $filename = wp_unique_filename($uploads['path'], basename($file));

            // copy the file to the uploads dir
            $new_file = $uploads['path'] . '/' . $filename;
            if (false === @copy($file, $new_file))
                return new WP_Error('upload_error', sprintf(__('The selected file could not be copied to %s.', 'add-from-server'), $uploads['path']));

            // Set correct file permissions
            $stat = stat(dirname($new_file));
            $perms = $stat['mode'] & 0000666;
            @chmod($new_file, $perms);
            // Compute the URL
            $url = $uploads['url'] . '/' . $filename;

            if ('file' == $import_date) {
                $time = gmdate('Y-m-d H:i:s', @filemtime($file));
            }
        }

        // Apply upload filters
        $return = apply_filters('wp_handle_upload', array('file' => $new_file, 'url' => $url, 'type' => $type));
        $new_file = $return['file'];
        $url = $return['url'];
        $type = $return['type'];

        $title = preg_replace('!\.[^.]+$!', '', basename($file));
        $content = $excerpt = '';

        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        if (preg_match('#^audio#', $type)) {
            $meta = wp_read_audio_metadata($new_file);

            if (!empty($meta['title'])) {
                $title = $meta['title'];
            }

            if (!empty($title)) {

                if (!empty($meta['album']) && !empty($meta['artist'])) {
                    /* translators: 1: audio track title, 2: album title, 3: artist name */
                    $content .= sprintf(__('"%1$s" from %2$s by %3$s.', 'add-from-server'), $title, $meta['album'], $meta['artist']);
                } elseif (!empty($meta['album'])) {
                    /* translators: 1: audio track title, 2: album title */
                    $content .= sprintf(__('"%1$s" from %2$s.', 'add-from-server'), $title, $meta['album']);
                } elseif (!empty($meta['artist'])) {
                    /* translators: 1: audio track title, 2: artist name */
                    $content .= sprintf(__('"%1$s" by %2$s.', 'add-from-server'), $title, $meta['artist']);
                } else {
                    $content .= sprintf(__('"%s".', 'add-from-server'), $title);
                }
            } elseif (!empty($meta['album'])) {

                if (!empty($meta['artist'])) {
                    /* translators: 1: audio album title, 2: artist name */
                    $content .= sprintf(__('%1$s by %2$s.', 'add-from-server'), $meta['album'], $meta['artist']);
                } else {
                    $content .= $meta['album'] . '.';
                }
            } elseif (!empty($meta['artist'])) {

                $content .= $meta['artist'] . '.';
            }

            if (!empty($meta['year']))
                $content .= ' ' . sprintf(__('Released: %d.'), $meta['year']);

            if (!empty($meta['track_number'])) {
                $track_number = explode('/', $meta['track_number']);
                if (isset($track_number[1]))
                    $content .= ' ' . sprintf(__('Track %1$s of %2$s.', 'add-from-server'), number_format_i18n($track_number[0]), number_format_i18n($track_number[1]));
                else
                    $content .= ' ' . sprintf(__('Track %1$s.', 'add-from-server'), number_format_i18n($track_number[0]));
            }

            if (!empty($meta['genre']))
                $content .= ' ' . sprintf(__('Genre: %s.', 'add-from-server'), $meta['genre']);

            // Use image exif/iptc data for title and caption defaults if possible.
        } elseif (0 === strpos($type, 'image/') && $image_meta = @wp_read_image_metadata($new_file)) {
            if (trim($image_meta['title']) && !is_numeric(sanitize_title($image_meta['title']))) {
                $title = $image_meta['title'];
            }

            if (trim($image_meta['caption'])) {
                $excerpt = $image_meta['caption'];
            }
        }

        if ($time) {
            $post_date_gmt = $time;
            $post_date = $time;
        } else {
            $post_date = current_time('mysql');
            $post_date_gmt = current_time('mysql', 1);
        }

        // Construct the attachment array
        $attachment = array(
            'post_mime_type' => $type,
            'guid' => $url,
            'post_parent' => $post_id,
            'post_title' => $title,
            'post_name' => $title,
            'post_content' => $content,
            'post_excerpt' => $excerpt,
            'post_date' => $post_date,
            'post_date_gmt' => $post_date_gmt
        );

        $attachment = apply_filters('afs-import_details', $attachment, $file, $post_id, $import_date);

        // WP < 4.4 Compat: ucfirt
        $new_file = str_replace(ucfirst(wp_normalize_path($uploads['basedir'])), $uploads['basedir'], $new_file);

        // Save the data
        $id = wp_insert_attachment($attachment, $new_file, $post_id);
        if (!is_wp_error($id)) {
            $data = wp_generate_attachment_metadata($id, $new_file);
            wp_update_attachment_metadata($id, $data);
            if (isset($data['file'])) {
                $filename = $data['file'];
            }
        }
        // update_post_meta( $id, '_wp_attached_file', $uploads['subdir'] . '/' . $filename );

        return basename($filename);
    }
}
