<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * License：MIT
 * Copyright ️© 2020-2021 www.jiangqie.com All rights reserved.
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
		foreach ($result as &$catgory) {
			$categories[] = [
				'id' => $catgory->term_id,
				'name' => $catgory->name,
				'description' => $catgory->description,
				'cover' => jiangqie_api_taxonomy_image_url($catgory->term_id)
			];
		}

		return $this->make_success($categories);
	}
}
