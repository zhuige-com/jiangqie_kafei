<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright ️© 2020-2022 www.jiangqie.com All rights reserved.
 */

// 我的设置
CSF::createSection($prefix, array(
    'id'    => 'profile',
    'title' => '我的设置',
    'icon'  => 'fas fa-user',
    'fields' => array(

        array(
            'id'      => 'profile_background',
            'type'    => 'media',
            'title'   => '背景图',
            'subtitle'   => '热门背景图',
            'library' => 'image',
        ),

        array(
            'id'       => 'profile_menu',
            'type'     => 'group',
            'title'    => '我的菜单',
            'subtitle' => '我的页面的菜单',
            'fields'   => array(

                array(
                    'id'          => 'tp',
                    'type'        => 'select',
                    'title'       => '类型',
                    'placeholder' => '选择类型',
                    'options'     => array(
                        'views'     => '我的浏览',
                        'likes'     => '我的点赞',
                        'favorites' => '我的收藏',
                        'comments'  => '我的评论',
                        // 'about'     => '关于我们',
                        'feedback'  => '意见反馈',
                        'contact'   => '在线客服',
                        'clear'     => '清除缓存',
                        'split'     => '段分割线',
                        'link'      => '自定义链接',
                        'page'      => '自定义页面',
                    ),
                ),

                array(
                    'id'      => 'icon',
                    'type'    => 'media',
                    'title'   => '图标',
                    'library' => 'image',
                    'dependency' => array('tp', '!=', 'split'),
                ),

                array(
                    'id'    => 'title',
                    'type'  => 'text',
                    'title' => '标题',
                    'dependency' => array('tp', '!=', 'split'),
                ),

                array(
                    'id'    => 'link',
                    'type'  => 'text',
                    'title' => '链接',
                    'dependency' => array('tp', '==', 'link'),
                    'after' => '<a href="https://www.jiangqie.com/ky/4673.html" target="_blank" title="页面路径怎么写？">页面路径</a>或小程序appid'
                ),

                array(
                    'id'          => 'page_id',
                    'type'        => 'select',
                    'title'       => '页面',
                    'chosen'      => true,
                    'ajax'        => true,
                    'options'     => 'pages',
                    'placeholder' => '请选择页面',
                    'dependency' => array('tp', '==', 'page'),
                ),

                array(
                    'id'    => 'line',
                    'type'  => 'switcher',
                    'title' => '分割线',
                    'subtitle' => '是否显示分割线?',
                    'dependency' => array('tp', '!=', 'split'),
                    'default' => '1'
                ),

                array(
                    'id'    => 'switch',
                    'type'  => 'switcher',
                    'title' => '开启/停用',
                    'default' => '1'
                ),
            ),
        ),
    )
));
