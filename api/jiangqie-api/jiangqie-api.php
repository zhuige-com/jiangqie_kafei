<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name:       JiangQie Mini Program API
 * Plugin URI:        https://www.jiangqie.com/
 * Description:       为小程序提供定制化API，让Wordpress快速变身小程序。
 * Version:           1.0.0
 * Author:            酱茄
 * Author URI:        https://www.jiangqie.com/
 * Text Domain:       jiangqie-api
 */

if (!defined('WPINC')) {
	die;
}

define('JIANG_QIE_API_PLUGIN_NAME', 'jiangqie-api');

/**
 * Store plugin base dir, for easier access later from other classes.
 * (eg. Include, pubic or admin)
 */
define('JIANG_QIE_API_BASE_DIR', plugin_dir_path(__FILE__));

/********************************************
 * RUN CODE ON PLUGIN UPGRADE AND ADMIN NOTICE
 *
 * @tutorial run_code_on_plugin_upgrade_and_admin_notice.php
 */
define('JIANG_QIE_API_BASE_NAME', plugin_basename(__FILE__));
// RUN CODE ON PLUGIN UPGRADE AND ADMIN NOTICE

/**
 * Initialize custom templater
 */
if (!class_exists('Exopite_Template')) {
	require_once plugin_dir_path(__FILE__) . 'includes/libraries/class-exopite-template.php';
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-jiangqie-api-activator.php
 */
function activate_jiangqie_api()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-jiangqie-api-activator.php';
	JiangQie_API_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-jiangqie-api-deactivator.php
 */
function deactivate_jiangqie_api()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-jiangqie-api-deactivator.php';
	JiangQie_API_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_jiangqie_api');
register_deactivation_hook(__FILE__, 'deactivate_jiangqie_api');

/*****************************************

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-jiangqie-api.php';

require plugin_dir_path(__FILE__) . 'includes/categories-images.php';

require plugin_dir_path(__FILE__) . 'includes/custom_users_column.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
// function run_jiangqie_api() {

// 	$plugin = new JiangQie_API();
// 	$plugin->run();

// }
// run_jiangqie_api();

/********************************************
 * THIS ALLOW YOU TO ACCESS YOUR PLUGIN CLASS
 * eg. in your template/outside of the plugin.
 *
 * Of course you do not need to use a global,
 * you could wrap it in singleton too,
 * or you can store it in a static class,
 * etc...
 *
 * @tutorial access_plugin_and_its_methodes_later_from_outside_of_plugin.php
 */
global $pbt_prefix_jiangqie_api;
$pbt_prefix_jiangqie_api = new JiangQie_API();
$pbt_prefix_jiangqie_api->run();
// END THIS ALLOW YOU TO ACCESS YOUR PLUGIN CLASS
