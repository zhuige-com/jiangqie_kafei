<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright ️© 2020-2022 www.jiangqie.com All rights reserved.
 */

class JiangQie_API_Category_Controller extends JiangQie_API_Base_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = 'category';
	}

	public function register_routes()
	{
		//获取所有分类
		register_rest_route($this->namespace, '/' . $this->module . '/index', [
			[
				'callback' => [$this, 'get_index'],
				'permission_callback' => '__return_true',
			]
		]);
	}

	/**
	 * 获取所有分类
	 */
	public function get_index($request)
	{
		$args = ['parent' => 0, 'hide_empty' => 0];
		$hide_cat = Jiangqie_API::option_value('hide_cat');
		if (!empty($hide_cat)) {
			$args['exclude'] = $hide_cat;
		}
		$result = get_categories($args);

		$categories = [];
		$default_cover = plugins_url("images/cat_cover.png", dirname(__FILE__));
		foreach ($result as &$catgory) {
			$cover = jiangqie_api_taxonomy_image_url($catgory->term_id);
			$categories[] = [
				'id' => $catgory->term_id,
				'name' => $catgory->name,
				'description' => $catgory->description,
				'cover' => $cover ? $cover : $default_cover
			];
		}

		return $this->make_success($categories);
	}
}
