<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright ️© 2020-2021 www.jiangqie.com All rights reserved.
 */

add_filter('manage_users_columns', 'jiangqie_api_manage_user_columns', 10, 2);
add_action('manage_users_custom_column', 'jiangqie_api_manage_user_custom_column', 10, 3);

function jiangqie_api_manage_user_columns($columns)
{
	unset($columns['name']);

	$new_columns = array();
	$new_columns['cb'] = $columns['cb'];
	$new_columns['username'] = $columns['username'];
	$new_columns['jqnickname'] = '昵称';

	unset($columns['cb']);
	unset($columns['username']);

	return array_merge($new_columns, $columns);
}

function jiangqie_api_manage_user_custom_column($value, $column_name, $user_id)
{
	if ('jqnickname' == $column_name) {
		$value = get_user_meta($user_id, 'nickname', true);
	}

	return $value;
}

add_filter('get_avatar', 'jiangqie_api_get_avatar', 10, 2);
function jiangqie_api_get_avatar($avatar, $id_or_email, $size = 96, $default = '', $alt = '', $args = null )  {
	$jq_avatar = get_user_meta($id_or_email, 'jiangqie_avatar', true);
	if ($jq_avatar) {
		return "<img src='$jq_avatar' class='avatar avatar-32 photo' height='32' width='32'>";
	} else {
		return $avatar;
	}
}