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
}
