<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * Copyright ️© 2020-2021 www.jiangqie.com All rights reserved.
 */

// 热榜设置
CSF::createSection($prefix, array(
    'id'    => 'other',
    'title' => '热榜设置',
    'icon'  => 'fas fa-plus-circle',
    'fields' => array(
        
        array(
            'id'      => 'hot_background',
            'type'    => 'media',
            'title'   => '背景图',
            'subtitle'   => '热门背景图',
            'library' => 'image',
        ),

        array(
            'id'          => 'hot_title',
            'type'        => 'text',
            'title'       => '分类标题',
            'placeholder' => '请输入热门标题'
        ),

        array(
            'id'          => 'hot_description',
            'type'        => 'text',
            'title'       => '分类描述',
            'placeholder' => '请输入热门描述'
        ),

    )
));
