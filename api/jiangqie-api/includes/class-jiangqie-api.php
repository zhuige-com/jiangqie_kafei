<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * Copyright ️ 2020 www.jiangqie.com All rights reserved.
 */

class JiangQie_API
{
	//分页 每页数量
	public const POSTS_PER_PAGE = 10;
	
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      JiangQie_API_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $jiangqie_api    The string used to uniquely identify this plugin.
	 */
	protected $jiangqie_api;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/*************************************************************
	 * ACCESS PLUGIN AND ITS METHODES LATER FROM OUTSIDE OF PLUGIN
	 *
	 * @tutorial access_plugin_and_its_methodes_later_from_outside_of_plugin.php
	 */
	/**
	 * Store plugin admin class to allow public access.
	 *
	 * @since    20180622
	 * @var object      The admin class.
	 */
	public $admin;


	/**
	 * Store plugin public class to allow public access.
	 *
	 * @since    20180622
	 * @var object      The admin class.
	 */
	public $public;
	// END ACCESS PLUGIN AND ITS METHODES LATER FROM OUTSIDE OF PLUGIN

	/*************************************************************
	 * ACCESS PLUGIN ADMIN PUBLIC METHODES FROM INSIDE
	 *
	 * @tutorial access_plugin_admin_public_methodes_from_inside.php
	 */
	/**
	 * Store plugin main class to allow public access.
	 *
	 * @since    20180622
	 * @var object      The main class.
	 */
	public $main;
	// ACCESS PLUGIN ADMIN PUBLIC METHODES FROM INSIDE

	/**
	 * 获取配置
	 */
	public static function option_value($key)
    {
        $options = get_option('jiangqie-api');
		if (isset($options[$key]) && !empty($options[$key])) {
			return $options[$key];
		}

		return false;
    }

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{

		$this->jiangqie_api = 'jiangqie-api';
		$this->version = '1.0.0';

		/*************************************************************
		 * ACCESS PLUGIN ADMIN PUBLIC METHODES FROM INSIDE
		 *
		 * @tutorial access_plugin_admin_public_methodes_from_inside.php
		 */
		$this->main = $this;
		// ACCESS PLUGIN ADMIN PUBLIC METHODES FROM INSIDE

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - JiangQie_API_Loader. Orchestrates the hooks of the plugin.
	 * - JiangQie_API_i18n. Defines internationalization functionality.
	 * - JiangQie_API_Admin. Defines all hooks for the admin area.
	 * - JiangQie_API_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-jiangqie-api-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-jiangqie-api-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-jiangqie-api-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-jiangqie-api-public.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/rest/class-jiangqie-api-base-controller.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/rest/class-jiangqie-api-setting-controller.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/rest/class-jiangqie-api-comment-controller.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/rest/class-jiangqie-api-post-controller.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/rest/class-jiangqie-api-category-controller.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/rest/class-jiangqie-api-user-controller.php';

		/**
		 * The class responsible for defining all actions for AJAX
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-jiangqie-api-ajax.php';

		/**************************************
		 * EXOPITE SIMPLE OPTIONS FRAMEWORK
		 *
		 * Get Exopite Simple Options Framework
		 *
		 * @link https://github.com/JoeSz/Exopite-Simple-Options-Framework
		 * @link https://www.joeszalai.org/exopite/exopite-simple-options-framework/
		 *
		 * @tutorial app_option_page_for_plugin_with_options_framework.php
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/exopite-simple-options/exopite-simple-options-framework-class.php';
		// END EXOPITE SIMPLE OPTIONS FRAMEWORK



		$this->loader = new JiangQie_API_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the JiangQie_API_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new JiangQie_API_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		// $plugin_admin = new JiangQie_API_Admin( $this->get_jiangqie_api(), $this->get_version() );

		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		/*************************************************************
		 * ACCESS PLUGIN ADMIN PUBLIC METHODES FROM INSIDE
		 * (COMBINED WITH ACCESS PLUGIN AND ITS METHODES LATER FROM OUTSIDE OF PLUGIN)
		 *
		 *
		 * @tutorial access_plugin_admin_public_methodes_from_inside.php
		 */
		$this->admin = new JiangQie_API_Admin($this->get_jiangqie_api(), $this->get_version(), $this->main);
		// END ACCESS PLUGIN ADMIN PUBLIC METHODES FROM INSIDE

		/*************************************************************
		 * ACCESS PLUGIN AND ITS METHODES LATER FROM OUTSIDE OF PLUGIN
		 *
		 * @tutorial access_plugin_and_its_methodes_later_from_outside_of_plugin.php
		 */
		// $this->admin = new JiangQie_API_Admin( $this->get_jiangqie_api(), $this->get_version() );

		$this->loader->add_action('admin_enqueue_scripts', $this->admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $this->admin, 'enqueue_scripts');
		// END ACCESS PLUGIN AND ITS METHODES LATER FROM OUTSIDE OF PLUGIN

		/**************************
		 * CREATE CUSTOM POST TYPES
		 *
		 * The problem with the initial activation code is that when the activation hook runs, it's after the init hook has run,
		 * so hooking into init from the activation hook won't do anything.
		 * You don't need to register the CPT within the activation function unless you need rewrite rules to be added
		 * via flush_rewrite_rules() on activation. In that case, you'll want to register the CPT normally, via the
		 * loader on the init hook, and also re-register it within the activation function and
		 * call flush_rewrite_rules() to add the CPT rewrite rules.
		 *
		 * @link https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/issues/261
		 *
		 * @tutorial custom_post_types.php
		 */
		//$plugin_post_types = new JiangQie_API_Post_Types();
		//$this->loader->add_action( 'init', $plugin_post_types, 'create_custom_post_type', 999 );
		// END CREATE CUSTOM POST TYPES

		/***********************************
		 * EXOPITE SIMPLE OPTIONS FRAMEWORK
		 *
		 * Save/Update our plugin options
		 *
		 * @tutorial app_option_page_for_plugin_with_options_framework.php
		 */
		$this->loader->add_action('init', $this->admin, 'create_menu');
		// END EXOPITE SIMPLE OPTIONS FRAMEWORK

		/********************************************
		 * RUN CODE ON PLUGIN UPGRADE AND ADMIN NOTICE
		 *
		 * @tutorial run_code_on_plugin_upgrade_and_admin_notice.php
		 */
		/**
		 * This function runs when WordPress completes its upgrade process
		 * It iterates through each plugin updated to see if ours is included
		 * @param $upgrader_object Array
		 * @param $options Array
		 */
		$this->loader->add_action('upgrader_process_complete', $this->admin, 'upgrader_process_complete', 10, 2);

		/**
		 * Show a notice to anyone who has just updated this plugin
		 * This notice shouldn't display to anyone who has just installed the plugin for the first time
		 */
		$this->loader->add_action('admin_notices', $this->admin, 'display_update_notice');
		// RUN CODE ON PLUGIN UPGRADE AND ADMIN NOTICE

		/**
		 * Add new image size.
		 *
		 * @link https://wordpress.stackexchange.com/questions/54423/add-image-size-in-a-plugin-i-created/304941#304941
		 */
		$this->loader->add_action('after_setup_theme', $this->admin, 'add_thumbnail_size');

		/**************************************************************
		 * ADD/REMOVE/REORDER/SORT CUSTOM POST TYPE LIST COLUMNS (test)
		 *
		 * @tutorial add_remove_reorder_sort_custom_post_type_list_columns_in_admin_area.php
		 */
		/**
		 * Modify columns in tests list in admin area.
		 *
		 * The hooks to create custom columns and their associated data for a custom post type
		 * are manage_{$post_type}_posts_columns and
		 * manage_{$post_type}_{$post_type_type}_custom_column or manage_{$post_type_hierarchical}_custom_column respectively,
		 * where {$post_type} is the name of the custom post type and {$post_type_hierarchical} is post or page.
		 *
		 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/manage_posts_custom_column
		 * @link https://wordpress.stackexchange.com/questions/253640/adding-custom-columns-to-custom-post-types/253644#253644
		 */
		$this->loader->add_filter('manage_test_posts_columns', $this->admin, 'manage_test_posts_columns');
		$this->loader->add_action('manage_posts_custom_column', $this->admin, 'manage_posts_custom_column', 10, 2);

		$this->loader->add_action('admin_head', $this->admin, 'add_style_to_admin_head');

		/**
		 * Filters the list table sortable columns for a specific screen.
		 * manage_{$this->screen->id}_sortable_columns
		 *
		 * @link https://developer.wordpress.org/reference/hooks/manage_this-screen-id_sortable_columns/
		 * @link https://www.smashingmagazine.com/2017/12/customizing-admin-columns-wordpress/
		 */
		$this->loader->add_filter('manage_edit-test_sortable_columns', $this->admin, 'manage_sortable_columns');
		$this->loader->add_action('pre_get_posts', $this->admin, 'manage_posts_orderby');
		// END ADD/REMOVE/REORDER/SORT CUSTOM POST TYPE LIST COLUMNS (test)

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$this->public = new JiangQie_API_Public($this->get_jiangqie_api(), $this->get_version(), $this->main);
		$this->loader->add_action('wp_enqueue_scripts', $this->public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $this->public, 'enqueue_scripts');

		$this->loader->add_action('jiangqie_post_thumbnail', $this->public, 'filter_post_thumbnail');
		$this->loader->add_filter('jiangqie_post_for_list', $this->public, 'filter_post_for_list');
		$this->loader->add_filter('jiangqie_post_for_detail', $this->public, 'filter_post_for_detail');
		$this->loader->add_filter('jiangqie_tag_for_list', $this->public, 'filter_tag_for_list');
		$this->loader->add_filter('jiangqie_tag_for_detail', $this->public, 'filter_tag_for_detail');

		$controller = [
			new JiangQie_API_Setting_Controller(),
			new JiangQie_API_Comment_Controller(),
			new JiangQie_API_Post_Controller(),
			new JiangQie_API_Category_Controller(),
			new JiangQie_API_User_Controller()
		];
		foreach ($controller as $control) {
			$this->loader->add_action('rest_api_init', $control, 'register_routes');
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_jiangqie_api()
	{
		return $this->jiangqie_api;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    JiangQie_API_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
