<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * Copyright ️© 2020-2022 www.jiangqie.com All rights reserved.
 */

// 首页设置
CSF::createSection($prefix, array(
    'id'    => 'home',
    'title' => '首页设置',
    'icon'  => 'fas fa-home',
    'fields' => array(

        array(
            'id'          => 'home_hot_search',
            'type'        => 'text',
            'title'       => '热门搜索词',
            'subtitle'    => '搜索词,英文逗号分隔',
            'placeholder' => '酱茄,美食'
        ),

        array(
            'id'          => 'home_top_nav',
            'type'        => 'select',
            'title'       => '顶部导航',
            'placeholder' => '请选择分类',
            'options'     => 'categories',
            'chosen'      => true,
            'multiple'    => true,
        ),

        array(
            'id'          => 'top_slide',
            'type'        => 'select',
            'title'       => '幻灯片',
            'placeholder' => '请选择文章',
            'chosen'      => true,
            'multiple'    => true,
            'sortable'    => true,
            'ajax'        => true,
            'options'     => 'posts',
        ),

        array(
            'id'     => 'home_icon_nav',
            'type'   => 'group',
            'title'  => '导航项',
            'fields' => array(
                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'placeholder' => '标题'
                ),
                array(
                    'id'      => 'icon',
                    'type'    => 'media',
                    'title'   => '图标',
                    'library' => 'image',
                ),
                array(
                    'id'       => 'link',
                    'type'     => 'text',
                    'title'    => '链接',
                    'default'  => 'https://www.jiangqie.com',
                ),
                array(
                    'id'    => 'switch',
                    'type'  => 'switcher',
                    'title' => '开启/停用',
                    'default' => '1'
                ),
            ),
        ),

        array(
            'id'    => 'home_active_switch',
            'type'  => 'switcher',
            'title' => '活动区域图',
            'subtitle' => '是否显示活动区域图?',
        ),

        array(
            'id'         => 'home_active',
            'type'       => 'accordion',
            'title'      => '活动区域图',
            'accordions' => array(

                array(
                    'title'  => '左图',
                    'fields' => array(
                        array(
                            'id'    => 'left_image',
                            'type'  => 'media',
                            'title' => '图片',
                            'library' => 'image',
                        ),
                        array(
                            'id'    => 'left_title',
                            'type'  => 'text',
                            'title' => '标题',
                        ),
                        array(
                            'id'    => 'left_link',
                            'type'  => 'text',
                            'title' => '链接',
                            'after' => '<a href="https://www.jiangqie.com/ky/4673.html" target="_blank" title="页面路径怎么写？">页面路径</a>或小程序appid'
                        ),
                    )
                ),

                array(
                    'title'  => '右上图',
                    'fields' => array(
                        array(
                            'id'    => 'right_top_image',
                            'type'  => 'media',
                            'title' => '图片',
                            'library' => 'image',
                        ),
                        array(
                            'id'    => 'right_top_title',
                            'type'  => 'text',
                            'title' => '标题',
                        ),
                        array(
                            'id'    => 'right_top_link',
                            'type'  => 'text',
                            'title' => '链接',
                            'after' => '<a href="https://www.jiangqie.com/ky/4673.html" target="_blank" title="页面路径怎么写？">页面路径</a>或小程序appid'
                        ),
                    )
                ),

                array(
                    'title'  => '右下图',
                    'fields' => array(
                        array(
                            'id'    => 'right_down_image',
                            'type'  => 'media',
                            'title' => '图片',
                            'library' => 'image',
                        ),
                        array(
                            'id'    => 'right_down_title',
                            'type'  => 'text',
                            'title' => '标题',
                        ),
                        array(
                            'id'    => 'right_down_link',
                            'type'  => 'text',
                            'title' => '链接',
                            'after' => '<a href="https://www.jiangqie.com/ky/4673.html" target="_blank" title="页面路径怎么写？">页面路径</a>或小程序appid'
                        ),
                    )
                ),

            )
        ),

        array(
            'id'          => 'home_hot',
            'type'        => 'select',
            'title'       => '热门文章',
            'placeholder' => '请选择文章',
            'chosen'      => true,
            'multiple'    => true,
            'sortable'    => true,
            'ajax'        => true,
            'options'     => 'posts',
        ),

        array(
            'id'          => 'home_list_mode',
            'type'        => 'select',
            'title'       => '列表模式',
            'subtitle'    => '首页文章列表显示方式',
            'chosen'      => true,
            'placeholder' => 'Select an option',
            'options'     => array(
                '3'         => '混合模式',
                '1'         => '小图模式',
                '2'         => '大图模式',
            ),
            'default'     => '3'
        ),

    )
));
