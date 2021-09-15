<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * License：MIT
 * Copyright ️© 2020-2021 www.jiangqie.com All rights reserved.
 */

class JiangQie_API_i18n
{
	public function load_plugin_textdomain()
	{
		load_plugin_textdomain(
			'jiangqie-api',
			false,
			dirname(JIANG_QIE_API_BASE_NAME) . '/languages/'
		);
	}
}
