<?php

/*
 * 酱茄小程序开源版
 * Author: 追格
 * Help document: https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright ️© 2020-2023 www.zhuige.com All rights reserved.
 */

class JiangQie_API
{
	//分页 每页数量
	const POSTS_PER_PAGE = 10;

	protected $loader;

	protected $jiangqie_api;

	protected $version;

	public $admin;

	public $public;

	public $main;

	/**
	 * 获取配置
	 */
	public static function option_value($key, $default = '')
	{
		static $options = false;
		if (!$options) {
			$options = get_option('jiangqie-api');
		}
		if (isset($options[$key]) && !empty($options[$key])) {
			return $options[$key];
		}

		return $default;
	}

	/**
	 * 图片配置项url
	 */
	public static function option_image_url($image, $default = '')
	{
		if ($image && isset($image['url']) && $image['url']) {
			return $image['url'];
		} else {
			if ($default) {
				return esc_url(plugins_url("public/images/$default", dirname(__FILE__)));
			} else {
				return $default;
			}
		}
	}

	/**
	 * 用户头像
	 */
	public static function user_avatar($user_id)
	{
		$avatar = get_user_meta($user_id, 'jiangqie_avatar', true);
		if (empty($avatar)) {
			$avatar = JIANG_QIE_API_BASE_URL . 'public/images/default_avatar.jpg';
		}
		return $avatar;
	}

	/**
	 * 微信token
	 */
	public static function get_wx_token()
	{
		// $path_token = JIANG_QIE_API_BASE_DIR . 'wx_access_token.data';
		// if (file_exists($path_token)) {
		// 	$str_token = file_get_contents($path_token);
		// 	$access_token = json_decode($str_token, TRUE);
		// 	if ($access_token['expires_in'] > time()) {
		// 		return $access_token;
		// 	}
		// }
		$access_token = get_option('jiangqie-api-wx-access-token');
		if ($access_token && isset($access_token['expires_in']) && $access_token['expires_in'] > time()) {
			return $access_token;
		}

		$app_id = JiangQie_API::option_value('app_id');
		$app_secret = JiangQie_API::option_value('app_secret');
		if (!$app_id || !$app_secret) {
			return false;
		}

		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $app_id . '&secret=' . $app_secret;
		$body = wp_remote_get($url);
		if (!is_array($body) || is_wp_error($body) || $body['response']['code'] != '200') {
			return false;
		}
		$access_token = json_decode($body['body'], TRUE);

		$access_token['expires_in'] = $access_token['expires_in'] + time() - 200;
		// file_put_contents($path_token, json_encode($access_token));
		update_option('jiangqie-api-wx-access-token', $access_token);

		return $access_token;
	}

	/**
	 * QQ token
	 */
	public static function get_qq_token()
	{
		// $path_token = JIANG_QIE_API_BASE_DIR . 'qq_access_token.data';
		// if (file_exists($path_token)) {
		// 	$str_token = file_get_contents($path_token);
		// 	$access_token = json_decode($str_token, TRUE);
		// 	if ($access_token['expires_in'] > time()) {
		// 		return $access_token;
		// 	}
		// }
		$access_token = get_option('jiangqie-api-qq-access-token');
		if ($access_token && isset($access_token['expires_in']) && $access_token['expires_in'] > time()) {
			return $access_token;
		}

		$app_id = JiangQie_API::option_value('qq_app_id');
		$app_secret = JiangQie_API::option_value('qq_app_secret');
		if (!$app_id || !$app_secret) {
			return false;
		}

		$url = "https://api.q.qq.com/api/getToken?grant_type=client_credential&appid=$app_id&secret=$app_secret";
		$body = wp_remote_get($url);
		if (!is_array($body) || is_wp_error($body) || $body['response']['code'] != '200') {
			return false;
		}
		$access_token = json_decode($body['body'], TRUE);

		$access_token['expires_in'] = $access_token['expires_in'] + time() - 200;
		// file_put_contents($path_token, json_encode($access_token));
		update_option('jiangqie-api-qq-access-token', $access_token);

		return $access_token;
	}

	/**
	 * 百度 token
	 */
	public static function get_bd_token()
	{
		// $path_token = JIANG_QIE_API_BASE_DIR . 'bd_access_token.data';
		// if (file_exists($path_token)) {
		// 	$str_token = file_get_contents($path_token);
		// 	$access_token = json_decode($str_token, TRUE);
		// 	if ($access_token['expires_in'] > time()) {
		// 		return $access_token;
		// 	}
		// }
		$access_token = get_option('jiangqie-api-bd-access-token');
		if ($access_token && isset($access_token['expires_in']) && $access_token['expires_in'] > time()) {
			return $access_token;
		}

		$app_id = JiangQie_API::option_value('bd_app_key');
		$app_secret = JiangQie_API::option_value('bd_app_secret');
		if (!$app_id || !$app_secret) {
			return false;
		}

		$url = "https://openapi.baidu.com/oauth/2.0/token?grant_type=client_credentials&client_id=$app_id&client_secret=$app_secret&scope=smartapp_snsapi_base
		";
		$body = wp_remote_get($url);
		if (!is_array($body) || is_wp_error($body) || $body['response']['code'] != '200') {
			return false;
		}
		$access_token = json_decode($body['body'], TRUE);

		$access_token['expires_in'] = $access_token['expires_in'] + time() - 200;
		// file_put_contents($path_token, json_encode($access_token));
		update_option('jiangqie-api-bd-access-token', $access_token);

		return $access_token;
	}

	public function __construct()
	{
		$this->jiangqie_api = 'jiangqie-api';
		$this->version = JIANG_QIE_API_VERSION;

		$this->main = $this;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_dependencies()
	{
		require_once JIANG_QIE_API_BASE_DIR . 'includes/class-jiangqie-api-loader.php';
		require_once JIANG_QIE_API_BASE_DIR . 'includes/class-jiangqie-api-i18n.php';
		require_once JIANG_QIE_API_BASE_DIR . 'admin/class-jiangqie-api-admin.php';

		/**
		 * rest api
		 */
		require_once JIANG_QIE_API_BASE_DIR . 'public/class-jiangqie-api-public.php';
		require_once JIANG_QIE_API_BASE_DIR . 'public/rest/class-jiangqie-api-base-controller.php';
		require_once JIANG_QIE_API_BASE_DIR . 'public/rest/class-jiangqie-api-setting-controller.php';
		require_once JIANG_QIE_API_BASE_DIR . 'public/rest/class-jiangqie-api-comment-controller.php';
		require_once JIANG_QIE_API_BASE_DIR . 'public/rest/class-jiangqie-api-other-controller.php';
		require_once JIANG_QIE_API_BASE_DIR . 'public/rest/class-jiangqie-api-post-controller.php';
		require_once JIANG_QIE_API_BASE_DIR . 'public/rest/class-jiangqie-api-category-controller.php';
		require_once JIANG_QIE_API_BASE_DIR . 'public/rest/class-jiangqie-api-user-controller.php';

		/**
		 * AJAX
		 */
		require_once JIANG_QIE_API_BASE_DIR . 'includes/class-jiangqie-api-ajax.php';

		/**
		 * 后台管理
		 */
		require_once JIANG_QIE_API_BASE_DIR . 'admin/codestar-framework/codestar-framework.php';

		$this->loader = new JiangQie_API_Loader();
	}

	private function set_locale()
	{
		$plugin_i18n = new JiangQie_API_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	private function define_admin_hooks()
	{
		if (!is_admin()) {
			return;
		}

		$this->admin = new JiangQie_API_Admin($this->get_jiangqie_api(), $this->get_version(), $this->main);

		$this->loader->add_action('admin_enqueue_scripts', $this->admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $this->admin, 'enqueue_scripts');

		$this->loader->add_action('init', $this->admin, 'create_menu', 0);
		$this->loader->add_action('admin_init', $this->admin, 'admin_init');
		$this->loader->add_action('admin_menu', $this->admin, 'admin_menu', 20);
	}

	private function define_public_hooks()
	{
		$this->public = new JiangQie_API_Public($this->get_jiangqie_api(), $this->get_version(), $this->main);
		$this->loader->add_action('wp_enqueue_scripts', $this->public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $this->public, 'enqueue_scripts');

		$this->loader->add_action('jiangqie_post_thumbnail', $this->public, 'filter_post_thumbnail');
		$this->loader->add_filter('jiangqie_post_for_list', $this->public, 'filter_post_for_list');
		$this->loader->add_filter('jiangqie_tag_for_list', $this->public, 'filter_tag_for_list');
		$this->loader->add_filter('jiangqie_tag_for_detail', $this->public, 'filter_tag_for_detail');

		$controller = [
			new JiangQie_API_Setting_Controller(),
			new JiangQie_API_Other_Controller(),
			new JiangQie_API_Comment_Controller(),
			new JiangQie_API_Post_Controller(),
			new JiangQie_API_Category_Controller(),
			new JiangQie_API_User_Controller()
		];
		foreach ($controller as $control) {
			$this->loader->add_action('rest_api_init', $control, 'register_routes');
		}
	}

	public function run()
	{
		$this->loader->run();
	}

	public function get_jiangqie_api()
	{
		return $this->jiangqie_api;
	}

	public function get_loader()
	{
		return $this->loader;
	}

	public function get_version()
	{
		return $this->version;
	}
}
