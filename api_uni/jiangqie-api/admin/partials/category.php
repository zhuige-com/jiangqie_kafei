<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * Copyright ️© 2020-2022 www.jiangqie.com All rights reserved.
 */

// 分类设置
CSF::createSection($prefix, array(
    'id'    => 'category',
    'title' => '分类设置',
    'icon'  => 'fas fa-coins',
    'fields' => array(
        
        array(
            'id'      => 'category_background',
            'type'    => 'media',
            'title'   => '背景图',
            'subtitle'   => '分类背景图',
            'library' => 'image',
        ),

        array(
            'id'          => 'category_title',
            'type'        => 'text',
            'title'       => '分类标题',
            'placeholder' => '请输入分类标题'
        ),

        array(
            'id'          => 'category_description',
            'type'        => 'text',
            'title'       => '分类描述',
            'placeholder' => '请输入分类描述'
        ),
        
    )
));
