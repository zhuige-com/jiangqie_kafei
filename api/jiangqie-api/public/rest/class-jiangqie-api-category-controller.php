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
		$result = get_categories(['parent' => 0, 'hide_empty' => 0]);

		$categories = [];
		foreach ($result as &$catgory) {
			$categories[] = [
				'id' => $catgory->term_id,
				'name' => $catgory->name,
				'description' => $catgory->description,
				'cover' => z_taxonomy_image_url($catgory->term_id)
			];
		}

		return $this->make_success($categories);
	}
}
