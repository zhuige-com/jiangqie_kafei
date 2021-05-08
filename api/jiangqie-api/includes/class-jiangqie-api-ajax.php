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

defined('ABSPATH') or die("Direct access to the script does not allowed");

class JiangQie_API_AJAX
{
    protected static $instance = null;

    private function __construct()
    {
        if (current_user_can('manage_options')) {
            add_action('wp_ajax_admin_backend_call', array($this, 'ajax_backend_call'));
        }

        add_action('wp_ajax_admin_frontend_call', array($this, 'ajax_frontend_call'));
        add_action('wp_ajax_nopriv_frontend_call', array($this, 'ajax_frontend_call'));
    }

    public static function get_instance()
    {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function ajax_backend_call()
    {
        // Security check
        check_ajax_referer('referer_id', 'nonce');

        $response = 'OK';
        // Send response in JSON format
        // wp_send_json( $response );
        // wp_send_json_error();
        wp_send_json_success($response);

        die();
    }

    public function ajax_frontend_call()
    {
        // Security check
        check_ajax_referer('referer_id', 'nonce');

        $response = 'OK';
        // Send response in JSON format
        // wp_send_json( $response );
        // wp_send_json_error();
        wp_send_json_success($response);

        die();
    }

}
