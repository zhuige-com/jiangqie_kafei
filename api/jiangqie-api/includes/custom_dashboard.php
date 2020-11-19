<?php
/*
 * 酱茄小程序PRO
 * Author: www.jiangqie.com
 * Copyright ️ 2020 www.jiangqie.com All rights reserved.
 */

function custom_dashboard_jiangqie_help()
{
	$content = '欢迎使用酱茄小程序';
	$res = wp_remote_get("https://key.jiangqie.com/api/goods/description?id=jq_xcx_free", ['timeout' => 1]);
	if (!is_wp_error($res) && $res['response']['code'] == 200) {
		$data = json_decode($res['body'], TRUE);
		if ($data['code'] == 1) {
			$content = $data['data'];
		}
	}

	echo $content;
}

function jiangqie_add_dashboard_widgets()
{
	wp_add_dashboard_widget('jiangqie_dashboard_widget', '酱茄Free小程序', 'custom_dashboard_jiangqie_help');
}

add_action('wp_dashboard_setup', 'jiangqie_add_dashboard_widgets');
