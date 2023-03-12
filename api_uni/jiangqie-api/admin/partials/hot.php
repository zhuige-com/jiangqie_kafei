<?php

/*
 * 酱茄小程序开源版
 * Author: 追格
 * Help document: https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright ️© 2020-2023 www.zhuige.com All rights reserved.
 */

// 热榜设置
CSF::createSection($prefix, array(
    'id'    => 'other',
    'title' => '热榜设置',
    'icon'  => 'fas fa-fire',
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
