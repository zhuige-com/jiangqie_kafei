<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * Copyright ️© 2020-2021 www.jiangqie.com All rights reserved.
 */

$content = '欢迎使用酱茄小程序! <br/><br/> 微信客服：jianbing2011 (加开源群、问题咨询、项目定制、购买咨询) <br/><br/> <a href="https://www.jiangqie.com/xz" target="_blank">更多免费产品</a>';
if (stripos($_SERVER["REQUEST_URI"], 'jiangqie-api')) {
    $res = wp_remote_get("https://key.jiangqie.com/api/goods/description?id=jq_xcx_free", ['timeout' => 1]);
    if (!is_wp_error($res) && $res['response']['code'] == 200) {
        $data = json_decode($res['body'], TRUE);
        if ($data['code'] == 1) {
            $content = $data['data'];
        }
    }
}

// 概要
CSF::createSection($prefix, array(
    'title'  => '概要',
    'icon'   => 'fas fa-rocket',
    'fields' => array(

        array(
            'type'    => 'content',
            'content' => $content,
        ),

    )
));
