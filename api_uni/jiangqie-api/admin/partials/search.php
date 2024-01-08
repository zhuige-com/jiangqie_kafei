<?php

/*
 * 酱茄小程序开源版
 * Author: 追格
 * Help document: https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright ️© 2020-2024 www.zhuige.com All rights reserved.
 */

// 分类设置
CSF::createSection($prefix, array(
    'id'    => 'search',
    'title' => '搜索设置',
    'icon'  => 'fas fa-search',
    'fields' => array(

        array(
            'id'          => 'home_hot_search',
            'type'        => 'text',
            'title'       => '热门搜索词',
            'subtitle'    => '搜索词,英文逗号分隔',
            'placeholder' => '酱茄,美食'
        ),

        array(
            'id'          => 'search_columns',
            'type'        => 'select',
            'title'       => '搜索字段',
            'placeholder' => '选择字段',
            'multiple'    => true,
            'chosen'      => true,
            'options'     => array(
                'post_title'    => '文章标题',
                'post_excerpt ' => '文章摘要',
                'post_content'  => '文章内容',
            ),
        ),

    )
));
