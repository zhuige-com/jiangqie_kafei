<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * Copyright ️© 2020-2021 www.jiangqie.com All rights reserved.
 */

function jiangqie_api_custom_dashboard_help()
{
	$content = '欢迎使用酱茄小程序! <br/><br/> 微信客服：jianbing2011 (加开源群、问题咨询、项目定制、购买咨询) <br/><br/> <a href="https://www.jiangqie.com/xz" target="_blank">更多免费产品</a>';
	$res = wp_remote_get("https://key.jiangqie.com/api/goods/description?id=jq_xcx_free", ['timeout' => 1]);
	if (!is_wp_error($res) && $res['response']['code'] == 200) {
		$data = json_decode($res['body'], TRUE);
		if ($data['code'] == 1) {
			$content = $data['data'];
		}
	}

	echo $content;
}

function jiangqie_api_add_dashboard_widgets()
{
	wp_add_dashboard_widget('jiangqie_dashboard_widget', '酱茄Free小程序', 'jiangqie_api_custom_dashboard_help');
}

add_action('wp_dashboard_setup', 'jiangqie_api_add_dashboard_widgets');
