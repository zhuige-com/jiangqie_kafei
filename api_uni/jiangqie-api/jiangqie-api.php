<?php

/**
 * Plugin Name:		JiangQie Free Mini Program
 * Plugin URI:		https://www.zhuige.com/product/zxfree.html
 * Description:		为小程序提供定制化API，让Wordpress快速变身小程序。
 * Version:			2.2.0
 * Author:			酱茄
 * Author URI:		https://www.zhuige.com/
 * License:			GPLv2 or later
 * License URI:		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain:		jiangqie-api
 */

if (!defined('WPINC')) {
	die;
}

define('JIANG_QIE_API_VERSION', '2.1.2');
define('JIANG_QIE_API_BASE_DIR', plugin_dir_path(__FILE__));
define('JIANG_QIE_API_BASE_NAME', plugin_basename(__FILE__));
define('JIANG_QIE_API_BASE_URL', plugin_dir_url(__FILE__));

function activate_jiangqie_api()
{
	require_once JIANG_QIE_API_BASE_DIR . 'includes/class-jiangqie-api-activator.php';
	JiangQie_API_Activator::activate();
}

function deactivate_jiangqie_api()
{
	require_once JIANG_QIE_API_BASE_DIR . 'includes/class-jiangqie-api-deactivator.php';
	JiangQie_API_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_jiangqie_api');
register_deactivation_hook(__FILE__, 'deactivate_jiangqie_api');

function jiangqie_api_action_links($actions)
{
	$actions[] = '<a href="admin.php?page=jiangqie-api">设置</a>';
	$actions[] = '<a href="https://www.zhuige.com/product/zxfree.html" target="_blank">技术支持</a>';
    return $actions;
}
add_filter('plugin_action_links_' . JIANG_QIE_API_BASE_NAME, 'jiangqie_api_action_links');

require JIANG_QIE_API_BASE_DIR . 'includes/class-jiangqie-api.php';
require JIANG_QIE_API_BASE_DIR . 'includes/zhuige-market.php';
require JIANG_QIE_API_BASE_DIR . 'includes/jiangqie-function.php';
require JIANG_QIE_API_BASE_DIR . 'includes/categories-images.php';
require JIANG_QIE_API_BASE_DIR . 'includes/custom_users_column.php';
require JIANG_QIE_API_BASE_DIR . 'includes/custom_dashboard.php';

global $pbt_prefix_jiangqie_api;
$pbt_prefix_jiangqie_api = new JiangQie_API();
$pbt_prefix_jiangqie_api->run();
