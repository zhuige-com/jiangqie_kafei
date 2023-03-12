<?php

/*
 * 酱茄小程序开源版
 * Author: 追格
 * Help document: https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright ️© 2020-2023 www.zhuige.com All rights reserved.
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
        wp_enqueue_style($this->jiangqie_api, JIANG_QIE_API_BASE_URL . 'admin/css/jiangqie-api-admin.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script($this->jiangqie_api, JIANG_QIE_API_BASE_URL . 'admin/js/jiangqie-api-admin.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->jiangqie_api . '_edit_extend', JIANG_QIE_API_BASE_URL . 'admin/js/jiangqie-api-edit-extend.js', array('quicktags'), $this->version, false);
    }

    public function create_menu()
	{
		$prefix = 'jiangqie-api';

		CSF::createOptions($prefix, array(
            'framework_title' => '酱茄Free小程序 <small>by <a href="https://www.zhuige.com" target="_blank" title="酱茄Free小程序">www.zhuige.com</a></small>',
			'menu_title' => '酱茄Free小程序',
			'menu_slug'  => 'jiangqie-api',
			'menu_position' => 2,
            'show_bar_menu' => false,
            'show_sub_menu' => false,
            'footer_credit' => 'Thank you for creating with <a href="https://www.zhuige.com/" target="_blank">酱茄</a>',
            'menu_icon' => 'dashicons-layout',
		));

		$base_dir = plugin_dir_path(__FILE__);
		$base_url = plugin_dir_url(__FILE__);

		require_once $base_dir . 'partials/overview.php';
		require_once $base_dir . 'partials/global.php';
		require_once $base_dir . 'partials/home.php';
        require_once $base_dir . 'partials/category.php';
        require_once $base_dir . 'partials/hot.php';
        require_once $base_dir . 'partials/ad.php';
		require_once $base_dir . 'partials/profile.php';
        require_once $base_dir . 'partials/article.php';

        //
        // 备份
        //
        CSF::createSection($prefix, array(
            'title'       => '备份',
            'icon'        => 'fas fa-shield-alt',
            'fields'      => array(
                array(
                    'type' => 'backup',
                ),
            )
        ));

        //过滤ID - 修复多选情况下 ID丢失造成的bug
		function jiangqie_api_sanitize_ids($ids, $type='') {
			if (!is_array($ids)) {
				return $ids;
			}

			$ids_n = [];
			foreach ($ids as $id) {
				if (($type=='cat' && get_category($id))) {
					$ids_n[] = $id;
				} else if ($type=='post' || $type=='page') {
					$post = get_post($id);
					if ($post && $post->post_status == 'publish') {
						$ids_n[] = $id;
					}
				}
			}
			return $ids_n;
		}

		function jiangqie_api_save_before( &$data, $option ) {
			$data['hide_cat'] = jiangqie_api_sanitize_ids($data['hide_cat'], 'cat');
			$data['home_top_nav'] = jiangqie_api_sanitize_ids($data['home_top_nav'], 'cat');
			$data['top_slide'] = jiangqie_api_sanitize_ids($data['top_slide'], 'post');
			$data['home_hot'] = jiangqie_api_sanitize_ids($data['home_hot'], 'post');
			
			return $data;
		}
		add_filter( 'csf_jiangqie-api_save', 'jiangqie_api_save_before', 10, 2 );
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
        wp_redirect('https://www.zhuige.com/docs/zxfree.html');
        die;
      }

      if ('jiangqie_xcx_free_upgrade' === $_GET['page']) {
        wp_redirect('https://www.zhuige.com/product/zxfree.html');
        die;
      }
    }
}
