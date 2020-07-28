<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * Copyright ️ 2020 www.jiangqie.com All rights reserved.
 */

class JiangQie_API_Setting_Controller extends JiangQie_API_Base_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = 'setting';
	}

	public function register_routes()
	{
		//首页
		register_rest_route($this->namespace, '/' . $this->module . '/home', [
			[
				'callback' => [$this, 'get_home'],
			]
		]);

		//热门
		register_rest_route($this->namespace, '/' . $this->module . '/hot', [
			[
				'callback' => [$this, 'get_hot'],
			]
		]);

		//分类
		register_rest_route($this->namespace, '/' . $this->module . '/category', [
			[
				'callback' => [$this, 'get_category'],
			]
		]);

		//用户中心
		register_rest_route($this->namespace, '/' . $this->module . '/ucenter', [
			[
				'callback' => [$this, 'get_ucenter'],
			]
		]);

	}

	/**
	 * 获取配置 首页
	 */
	public function get_home($request)
	{
		//LOGO
		$data['logo'] = JiangQie_API::option_value('logo');

		//小程序名称
		$data['title'] = JiangQie_API::option_value('title');

		//顶部分类
		$cat_ids = JiangQie_API::option_value('home_top_nav');
		$args = ['hide_empty' => 0];
		if (!empty($cat_ids)) {
			$args['include'] = $cat_ids;
		}

		$result = get_categories($args);

		$categories = [];
		$cat_ids = explode(',', $cat_ids);
		foreach ($cat_ids as $cat_id) {
			foreach ($result as $item) {
				if ($cat_id == $item->term_id) {
					$categories[] = [
						'id' => $item->term_id,
						'name' => $item->name,
					];
					break;
				}
			}
		}

		$data['top_nav'] = $categories;

		//幻灯片
		$slide_ids = JiangQie_API::option_value('top_slide');
		$slides = [];
		if (!empty($slide_ids)) {
			$slide_ids = explode(',', $slide_ids);
			$args = [
				'posts_per_page' => JiangQie_API::POSTS_PER_PAGE,
				'post__in' => $slide_ids,
			];

			$query = new WP_Query();
			$result = $query->query($args);

			foreach ($slide_ids as $slide_id) {
				foreach ($result as $item) {
					if ($slide_id == $item->ID) {
						$slides[] = [
							'id' => $item->ID,
							'thumbnail' => apply_filters('jiangqie_post_thumbnail', $item->ID)
						];
					}
				}
			}
		}
		$data['slide'] = $slides;

		//图标导航
		$icon_nav_org = JiangQie_API::option_value('home_icon_nav');
		$icon_nav = [];
		foreach ($icon_nav_org as $item) {
			if ($item['enable'] == 'yes') {
				$icon_nav[] = $item;
			}
		}
		$data['icon_nav'] = $icon_nav;

		//活动区域
		$home_active = JiangQie_API::option_value('home_active');
		if (!empty($home_active) && !empty($home_active['left_image']) && !empty($home_active['right_top_image']) && !empty($home_active['right_down_image'])) {
			$data['actives'] = [
				'left' => [
					'image' => $home_active['left_image'],
					'title' => $home_active['left_title'],
					'link' => $home_active['left_link']
				],
				'right_top' => [
					'image' => $home_active['right_top_image'],
					'title' => $home_active['right_top_title'],
					'link' => $home_active['right_top_link']
				],
				'right_down' => [
					'image' => $home_active['right_down_image'],
					'title' => $home_active['right_down_title'],
					'link' => $home_active['right_down_link']
				],
			];
		} else {
			$data['actives'] = false;
		}

		//热门文章
		$hot_ids = JiangQie_API::option_value('home_hot');
		$hots = [];
		if (!empty($hot_ids)) {
			$hot_ids = explode(',', $hot_ids);
			$args = [
				'posts_per_page' => JiangQie_API::POSTS_PER_PAGE,
				'post__in' => $hot_ids,
			];

			$query = new WP_Query();
			$result = $query->query($args);

			foreach ($hot_ids as $hot_id) {
				foreach ($result as $item) {
					if ($hot_id == $item->ID) {
						$hots[] = [
							'id' => $item->ID,
							'title' => $item->post_title,
							'thumbnail' => apply_filters('jiangqie_post_thumbnail', $item->ID)
						];
					}
				}
			}
		}
		$data['hot'] = $hots;

		//列表模式
		$data['list_mode'] = JiangQie_API::option_value('home_list_mode');
		if (!$data['list_mode']) {
			$data['list_mode'] = 3;
		}

		return $this->make_success($data);
	}

	/**
	 * 获取配置 热门
	 */
	public function get_hot()
	{
		$data = [
			'background' => JiangQie_API::option_value('hot_background'),
			'title' => JiangQie_API::option_value('hot_title'),
			'description' => JiangQie_API::option_value('hot_description'),
		];

		return $this->make_success($data);
	}

	/**
	 * 获取配置 分类
	 */
	public function get_category()
	{
		$data = [
			'background' => JiangQie_API::option_value('category_background'),
			'title' => JiangQie_API::option_value('category_title'),
			'description' => JiangQie_API::option_value('category_description'),
		];

		return $this->make_success($data);
	}

	/**
	 * 获取配置 用户中心
	 */
	public function get_ucenter()
	{
		$result = JiangQie_API::option_value('profile_menu');
		if (empty($result)) {
			$menu = [];
		} else {
			foreach ($result as $item) {
				if ($item['enable'] == 'yes') {
					$menu[] = $item;
				}
			}
		}
		
		$data = [
			'background' => JiangQie_API::option_value('profile_background'),
			'copyright' => JiangQie_API::option_value('copyright_logo'),
			'menu' => $menu,
		];

		return $this->make_success($data);
	}
	
}
