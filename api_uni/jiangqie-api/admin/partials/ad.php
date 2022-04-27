<?php

//
// 广告
//
CSF::createSection($prefix, array(
    'id'    => 'ad',
    'title' => '广告设置',
    'icon'  => 'fa fa-usd',
    'fields' => array(

        array(
            'id'     => 'wx_ad_home',
            'type'   => 'fieldset',
            'title'  => '首页',
            'subtitle' => '插屏广告',
            'fields' => array(
                array(
                    'id'    => 'adid',
                    'type'  => 'text',
                    'title' => '广告位ID',
                ),
                array(
                    'id'      => 'delay',
                    'type'    => 'number',
                    'title'   => '延时弹出',
                    'unit'    => '秒',
                    'default' => '15',
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
            'id'     => 'wx_ad_category',
            'type'   => 'fieldset',
            'title'  => '分类页',
            'subtitle' => 'Banner广告',
            'fields' => array(
                array(
                    'id'    => 'adid',
                    'type'  => 'text',
                    'title' => '广告位ID',
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
            'id'     => 'wx_ad_article_top',
            'type'   => 'fieldset',
            'title'  => '文章页-顶部',
            'subtitle' => 'Banner广告',
            'fields' => array(
                array(
                    'id'    => 'adid',
                    'type'  => 'text',
                    'title' => '广告位ID',
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
            'id'     => 'wx_ad_article_bottom',
            'type'   => 'fieldset',
            'title'  => '文章页-底部',
            'subtitle' => 'Banner广告',
            'fields' => array(
                array(
                    'id'    => 'adid',
                    'type'  => 'text',
                    'title' => '广告位ID',
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
