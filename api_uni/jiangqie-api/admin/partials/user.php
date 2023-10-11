<?php

/*
 * 酱茄小程序开源版
 * Author: 追格
 * Help document: https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright ️© 2020-2023 www.zhuige.com All rights reserved.
 */

// 登录注销
CSF::createSection($prefix, array(
    'id'    => 'log_in_out',
    'title' => '登录注销',
    'icon'  => 'fas fa-user-plus',
    'fields' => array(

        array(
            'id'          => 'user_login_ystk',
            'type'        => 'select',
            'title'       => '隐私条款',
            'placeholder' => '请选择页面',
            'options'     => 'pages',
            'chosen'      => true,
            'multiple'    => false,
            'ajax'        => true,
        ),

        array(
            'id'          => 'user_login_yhxy',
            'type'        => 'select',
            'title'       => '用户协议',
            'placeholder' => '请选择页面',
            'options'     => 'pages',
            'chosen'      => true,
            'multiple'    => false,
            'ajax'        => true,
        ),

        array(
            'id'    => 'user_logout_explain',
            'type'  => 'wp_editor',
            'title' => '注销说明',
        ),

    )
));
