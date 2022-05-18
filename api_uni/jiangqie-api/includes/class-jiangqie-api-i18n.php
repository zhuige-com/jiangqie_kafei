<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright ️© 2020-2022 www.jiangqie.com All rights reserved.
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
