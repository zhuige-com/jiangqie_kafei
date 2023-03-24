<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!defined('ZHUIGE_MARKET')) {
	define('ZHUIGE_MARKET', 1);

	function zhuige_market_callback()
	{
?>
		<div class="zhuige-market">
			<div class="zhuige-market-nav">
				<h1>追格应用市场</h1>
				<input type="hidden" class="zhuige-param-init" value="1" />
				<ul>
					<li>
						<a href="https://www.zhuige.com/" target="_blank" title="追格官网">追格官网</a>
					</li>
					<li>
						<a href="https://www.zhuige.com/docs.html" target="_blank" title="安装文档">安装文档</a>
					</li>
					<li>
						<a href="https://www.zhuige.com/bbs.html" target="_blank" title="用户社区">用户社区</a>
					</li>
					<li>
						<a href="https://www.zhuige.com/product.html" target="_blank" title="更多产品">更多产品</a>
					</li>
				</ul>
			</div>

			<div class="zhuige-market-box">

				<div class="zhuige-market-type">
					<ul class="zhuige-market-type-ul">
						<li>
							<text>产品分类</text>
						</li>
						<li class="zhuige-market-cat activ" data-id="">
							<a href="javascript:" title="全部">全部</a>
						</li>
					</ul>
					<ul>
						<li>
							<text>排序方式</text>
						</li>
						<li class="zhuige-market-free activ" data-free="">
							<a href="javascript:" title="全部">全部</a>
						</li>
						<li class="zhuige-market-free" data-free="0">
							<a href="javascript:" title="付费">付费</a>
						</li>
						<li class="zhuige-market-free" data-free="1">
							<a href="javascript:" title="免费">免费</a>
						</li>
					</ul>
				</div>

				<div class="zhuige-market-ad-box" style="display:none;">
					<div class="zhuige-market-ad">
						<ol class="zhuige-market-ad-ol">
						</ol>
					</div>
				</div>

				<div class="zhuige-theme-list">
					<!-- 列表内容 -->
				</div>
			</div>
		</div>
<?php
	}

	add_action('admin_menu', 'zhuige_market_add_menu_items');
	function zhuige_market_add_menu_items()
	{
		$zhuige_market_hook = add_menu_page(
			'追格应用市场',			 // Page title.
			'追格应用市场',			 // Menu title.
			'manage_options',		// Capability.
			'zhuige_market',		// Menu slug.
			'zhuige_market_callback',	// Callback function.
			'dashicons-store',		// 菜单图标
			3						// position
		);

		add_action('admin_print_scripts-' . $zhuige_market_hook, 'enqueue_script_zhuige_market');
		add_action('admin_print_styles-' . $zhuige_market_hook, 'enqueue_style_zhuige_market');
	}

	//加载js - 插件市场
	function enqueue_script_zhuige_market()
	{
		$url = get_template_directory_uri();
		wp_register_script('lib-layer', $url . '/js/layer/layer.js', ['jquery'], '1.0', false);
		wp_enqueue_script('zhuige-market-js', JIANG_QIE_API_BASE_URL . 'admin/js/zhuige-market.js', array('jquery', 'lib-layer'), '1.0.', true);
	}

	//加载css - 插件市场
	function enqueue_style_zhuige_market()
	{
		wp_enqueue_style('zhuige-market-css', JIANG_QIE_API_BASE_URL . 'admin/css/zhuige-market.css');
	}
}
