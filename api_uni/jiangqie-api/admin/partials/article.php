<?php

/*
 * 酱茄小程序开源版
 * Author: 追格
 * Help document: https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright ️© 2020-2023 www.zhuige.com All rights reserved.
 */

// 文章设置
CSF::createSection($prefix, array(
    'id'    => 'article',
    'title' => '文章设置',
    'icon'  => 'fas fa-file',
    'fields' => array(
        
        array(
            'id'    => 'switch_pre_next',
            'type'  => 'switcher',
            'title' => '导航',
            'subtitle' => '是否开启上一篇下一篇?',
            'default' => '1'
        ),

        array(
            'id'    => 'switch_comment',
            'type'  => 'switcher',
            'title' => '评论',
            'subtitle' => '是否开启评论功能?',
            'default' => '1'
        ),

        array(
            'id'    => 'switch_comment_mobile',
            'type'  => 'switcher',
            'title' => '评论要求',
            'subtitle' => '绑定手机号才能评论?',
            'default' => ''
        ),

        array(
            'id'    => 'switch_comment_verify',
            'type'  => 'switcher',
            'title' => '评论审核',
            'subtitle' => '评论是否需要审核?',
            'default' => '1'
        ),

    )
));