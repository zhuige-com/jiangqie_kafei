<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * Copyright ️ 2020 www.jiangqie.com All rights reserved.
 */

class JiangQie_API_Public
{

	private $jiangqie_api;

	private $version;

	public $main;

	public function __construct($jiangqie_api, $version, $plugin_main)
	{
		$this->jiangqie_api = $jiangqie_api;
		$this->version = $version;
		$this->main = $plugin_main;
	}

	public function enqueue_styles()
	{
		wp_enqueue_style($this->jiangqie_api, plugin_dir_url(__FILE__) . 'css/jiangqie-api-public.css', array(), $this->version, 'all');
	}

	public function enqueue_scripts()
	{
		wp_enqueue_script($this->jiangqie_api, plugin_dir_url(__FILE__) . 'js/jiangqie-api-public.js', array('jquery'), $this->version, false);
	}

	public function filter_post_for_list($posts)
	{
		$data = [];

		foreach ($posts as $post) {
			$item = [
				'id' => $post->ID,
				'time' => $post->post_date,
				'title' => $post->post_title,
				'comment_count' => $post->comment_count,
			];

			if ($post->post_excerpt) {
				$item["excerpt"] = html_entity_decode(wp_trim_words($post->post_excerpt, 50, '...'));
			} else {
				$item["excerpt"] = html_entity_decode(wp_trim_words($post->post_content, 50, '...'));
			}

			$item['thumbnail'] = apply_filters('jiangqie_post_thumbnail', $post->ID);

			$data[] = $item;
		}
		return $data;
	}

	public function filter_post_for_detail($posts)
	{
		$post = $posts[0];
		$data = [
			'id' => $post->ID,
			'time' => $post->post_date,
			'title' => $post->post_title,
			'content' => apply_filters('the_content', $post->post_content),
			'comment_count' => $post->comment_count,
			//'thumbnail' => apply_filters('jiangqie_post_thumbnail', $post->ID)
		];

		return $data;
	}

	public function filter_tag_for_list($tags)
	{
		$data = [];
		$all_tag_len = 0;
		foreach ($tags as $tag) {
			//计算字符串长度 兼容中英文
			preg_match_all("/./us", $tag->name, $match);
			$tag_name_len = count($match[0]);

			if ($all_tag_len + $tag_name_len > 6) {
				continue;
			}

			$data[] = [
				'id' => $tag->term_id,
				'name' => $tag->name
			];

			$all_tag_len += $tag_name_len;

			//列表中最多放2个标签 或 标签长度和大于6
			if (sizeof($data) > 1 || $all_tag_len == 6) {
				break;
			}
		}
		return $data;
	}

	public function filter_tag_for_detail($tags)
	{
		$data = [];
		foreach ($tags as $tag) {
			$data[] = [
				'id' => $tag->term_id,
				'name' => $tag->name
			];
		}
		return $data;
	}

	public function filter_post_thumbnail($post_id)
	{
		$thumbnails = get_post_meta($post_id, 'thumbnail', true);
		if (!empty($thumbnails)) {
			return $thumbnails;
		}

		if (has_post_thumbnail($post_id)) {
			$thumbnail_id = get_post_thumbnail_id($post_id);
			if ($thumbnail_id) {
				$attachment = wp_get_attachment_image_src($thumbnail_id, 'full');
				$thumbnails = $attachment[0];
				return $thumbnails;
			}

			$thumbnail_code = get_the_post_thumbnail($post_id, 'full');
			$thumbnail_src = '/src=\"(.*?)\"/';
			if (preg_match($thumbnail_src, $thumbnail_code, $thumbnail)) {
				$thumbnails = $thumbnail[1];
				return $thumbnails;
			}

			$thumbnails = JiangQie_API::option_value('default_thumbnail');
			return $thumbnails;
		}

		$post = get_post($post_id);
		$post_content = $post->post_content;

		preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i', do_shortcode($post_content), $matches);
		if ($matches && isset($matches[1]) && isset($matches[1][0])) {
			return $matches[1][0];
		}

		$thumbnails = JiangQie_API::option_value('default_thumbnail');
		return $thumbnails;
	}
}
