<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * Copyright ️© 2020-2021 www.jiangqie.com All rights reserved.
 */

class JiangQie_API_Admin
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
        wp_enqueue_style($this->jiangqie_api, plugin_dir_url(__FILE__) . 'css/jiangqie-api-admin.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script($this->jiangqie_api, plugin_dir_url(__FILE__) . 'js/jiangqie-api-admin.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->jiangqie_api . '_edit_extend', plugin_dir_url(__FILE__) . 'js/jiangqie-api-edit-extend.js', array('quicktags'), $this->version, false);
    }

    public function create_menu()
	{
		$prefix = 'jiangqie-api';

		CSF::createOptions($prefix, array(
            'framework_title' => '酱茄Free小程序 <small>by <a href="https://www.jiangqie.com" target="_blank" title="酱茄Free小程序">www.jiangqie.com</a></small>',
			'menu_title' => '酱茄Free小程序',
			'menu_slug'  => 'jiangqie-api',
			'menu_position' => 2,
            'footer_credit' => 'Thank you for creating with <a href="https://www.jiangqie.com/" target="_blank">酱茄</a>'
		));

		$base_dir = plugin_dir_path(__FILE__);
		$base_url = plugin_dir_url(__FILE__);

		require_once $base_dir . 'partials/overview.php';
		require_once $base_dir . 'partials/global.php';
		require_once $base_dir . 'partials/home.php';
        require_once $base_dir . 'partials/category.php';
        require_once $base_dir . 'partials/hot.php';
		require_once $base_dir . 'partials/profile.php';
	}

    public function admin_init()
	{
        $this->handle_external_redirects();
    }

    public function admin_menu()
	{
        add_submenu_page('jiangqie-api', '', '安装文档', 'manage_options', 'jiangqie_xcx_free_setup', array( &$this, 'handle_external_redirects' ) );
        add_submenu_page('jiangqie-api', '', '新版下载', 'manage_options', 'jiangqie_xcx_free_upgrade', array( &$this, 'handle_external_redirects' ) );
    }

    public function handle_external_redirects()
    {
      if (empty($_GET['page'])) {
        return;
      }

      if ('jiangqie_xcx_free_setup' === $_GET['page']) {
        wp_redirect('https://www.jiangqie.com/ky/4655.html');
        die;
      }

      if ('jiangqie_xcx_free_upgrade' === $_GET['page']) {
        wp_redirect('https://www.jiangqie.com/ky/4639.html');
        die;
      }
    }
}
