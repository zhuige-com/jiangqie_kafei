<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * License：MIT
 * Copyright ️ 2020 www.jiangqie.com All rights reserved.
 */

class JiangQie_API_Base_Controller extends WP_REST_Controller
{
	public function __construct()
	{
		$this->namespace = 'jiangqie/v1';
	}

	//组合返回值
	public function make_response($code, $msg, $data = null)
	{
		$response = [
			'code' => $code,
			'msg' => $msg,
		];

		if ($data !== null) {
			$response['data'] = $data;
		}

		return $response;
	}

	//组合返回值 成功
	public function make_success($data = null)
	{
		return $this->make_response(0, '操作成功！', $data);
	}

	//组合返回值 失败
	public function make_error($msg = '', $code = 1)
	{
		return $this->make_response($code, $msg);
	}

	/**
	 * 分页参数
	 */
	public function params_paging()
	{
		return [
			'offset' => [
				'default' => 0,
				'description' => '起始位置',
				'type' => 'integer',
			]
		];
	}

	/**
	 * 获取参数的方便方法
	 */
	public function param_value($request, $param_name, $default_value = false)
	{
		if (isset($request[$param_name])) {
			return $request[$param_name];
		}

		return $default_value;
	}

	/**
	 * 美化时间格式
	 */
	public function time_beautify($time)
	{
		$origin_time = strtotime($time);
		$dur = time() + 28800 - $origin_time;
		if ($dur < 60) {
			return '刚刚';
		} else if ($dur < 3600) {
			return floor($dur / 60) . '分钟前';
		} else if ($dur < 86400) {
			return floor($dur / 3600) . '小时前';
		} else if ($dur < 604800) { //7天内
			return floor($dur / 86400) . '天前';
		} else {
			return date("Y-m-d", $origin_time);
		}
	}

	/**
	 * 检查登陆
	 */
	public function check_login($request)
	{
		$token = $this->param_value($request, 'token', '');
		if (empty($token)) {
			return false;
		}

		global $wpdb;
		$table_usermeta = $wpdb->prefix . 'usermeta';
		$user_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM `$table_usermeta` WHERE  meta_key='jiangqie_token' AND meta_value='%s'", $token));

		return $user_id;
	}

	/**
	 * 获取WX session
	 */
	public function getWXSession()
	{
		$app_id = JiangQie_API::option_value('app_id');
		$app_secret = JiangQie_API::option_value('app_secret');
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $app_id . '&secret=' . $app_secret;
		$body = wp_remote_get($url);
		if (!is_array($body) || is_wp_error($body) || $body['response']['code'] != '200') {
			return false;
		}
		$access_token = json_decode($body['body'], true);
		return $access_token;
	}

	//把图片添加到媒体库
	public function handle_import_file($file, $post_id = 0, $import_date = 'current')
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
		}
		// update_post_meta( $id, '_wp_attached_file', $uploads['subdir'] . '/' . $filename );

		return $id;
	}

}
