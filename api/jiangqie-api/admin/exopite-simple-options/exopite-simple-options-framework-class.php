<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 * Last edit: 2019-12-03
 *
 * INFOS AND TODOS:
 * - fix: typography not working in group
 * - fix: typography font-weight not save/restore
 * - fix: if no group title, then take parents
 *
 * IDEAS
 * - import options from file
 * - chunk upload
 */
/**
 * ToDos:
 * - possibility to override indluded files from path
 */
/**
 * Available fields:
 * - ACE field
 * - attached
 * - backup
 * - button
 * - botton_bar
 * - card
 * - checkbox
 * - color
 * - content
 * - date
 * - editor
 * - gallery
 * - group/accordion item
 * - hidden
 * - image
 * - image_select
 * - meta
 * - notice
 * - number
 * - password
 * - radio
 * - range
 * - select
 * - switcher
 * - tab
 * - tap_list
 * - text
 * - textarea
 * - typography
 * - upload
 * - video mp4/oembed
 */
/**
 * Standard args for all field:
 * - type
 * - id
 * - title
 *   - description
 * - class
 * - attributes
 * - before
 * - after
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework' ) ) :

	class Exopite_Simple_Options_Framework {

		/**
		 *
		 * dirname
		 * @access public
		 * @var string
		 *
		 */
		public $dirname = '';

		/**
		 *
		 * unique
		 * @access public
		 * @var string
		 *
		 */
		public $unique = '';

		/**
		 *
		 * notice
		 * @access public
		 * @var boolean
		 *
		 */
		public $notice = false;

		/**
		 *
		 * settings
		 * @access public
		 * @var array
		 *
		 */
		public $config = array();

		/**
		 *
		 * options
		 * @access public
		 * @var array
		 *
		 */
		public $fields = array();
		public $elements = array();

		public $is_multilang = null;

		public $multilang = false;
		public $lang_default;
		public $lang_current;

		public $languages = array();

		public $version;

		// public $debug = true;
		public $debug = false;

		/**
		 *
		 * options store
		 * @access public
		 * @var array
		 *
		 */
		public $db_options = array();

		/**
		 * Sets the type to  metabox|menu
		 * @var string
		 */
		private $type;

		/**
		 * @var object WP_Error
		 */
		protected $errors;

		/**
		 * @var array required fields for $type = menu
		 */
		protected $required_keys_all_types = array();

		/**
		 * @var array required fields for $type = menu
		 */
		protected $required_keys_menu = array( 'id', 'menu_title', 'plugin_basename' );

		/**
		 * @var array required fields for $type = metabox
		 */
		protected $required_keys_metabox = array( 'id', 'post_types', 'title', 'capability' );

		public function __construct( $config, $elements ) {

			// If we are not in admin area exit.
			if ( ! is_admin() ) {
				return;
			}

			$this->version = '20191203';

			$this->unique = $config['id'];

			// Filter for override every exopite $config and $fields
			// $this->config = apply_filters( 'exopite_sof_config', $config );
			// $this->elements = apply_filters( 'exopite_sof_options', $fields );

			// Filter for override $config and $fields with respect to $config and $fields
			$this->config = apply_filters( 'exopite_sof_config_' . $this->unique, $config );
			$this->elements = apply_filters( 'exopite_sof_options_' . $this->unique, $elements );

			// now is_menu() and is_metabox() available

			$this->load_textdomain();

			$this->get_fields();

			$this->check_required_configuration_keys();

			$this->load_classes();

			$this->set_properties();

			$this->setup_multilang();

			$this->include_field_classes();

			$this->define_shared_hooks();

			$this->define_hooks();

		}

		public function get_mo_file() {
			$path = wp_normalize_path( dirname( __FILE__ ) ) . '/lang';
			$domain = 'exopite-sof';
			$locale = determine_locale();
			return $path . '/' . $domain . '-' . $locale . '.mo';
		}

		public function load_textdomain() {

			$mofile = $this->get_mo_file();
			load_textdomain( 'exopite-sof', $mofile );

		}

		protected function setup_multilang() {

			// Srt Defaults for all cases
			$multilang_defaults = Exopite_Simple_Options_Framework_Helper::get_language_defaults();

			if ( is_array( $multilang_defaults ) ) {

				$this->config['multilang'] = $this->multilang = $multilang_defaults;

				$this->lang_current = $this->multilang['current'];
				$this->lang_default = $this->multilang['default'];
				$this->languages    = $this->multilang['languages'];

			}

		}

		/**
		 * Return the array of languages except current language
		 *
		 * @return array $languages_except_current
		 *
		 */
		public function languages_except_current_language() {

			$all_languages = $this->languages;

			if ( empty( $all_languages ) ) {
				return $all_languages;
			}

			$languages_except_current = array_diff( $all_languages, array( $this->lang_current ) );

			unset( $all_languages );

			return $languages_except_current;

		}

		/**
		 * Checks for required keys in configuration array
		 * and throw admin error if a required key is missing
		 */
		protected function check_required_configuration_keys() {
			// instantiate the Wp_Error for $this->errors
			$this->errors = new WP_Error();

			$required_key_array = $this->required_keys_all_types;

			if ( $this->is_menu() ) {
				$required_key_array = $this->required_keys_menu;
			}

			if ( $this->is_metabox() ) {
				$required_key_array = $this->required_keys_metabox;
			}

			// Loop through all required keys array to check if every required key is set.
			if ( ! empty( $required_key_array ) && ! empty( $this->config ) ) {

				foreach ( $required_key_array as $key ) :

					if ( ! array_key_exists( $key, $this->config ) ) {
						// Add error message to the WP_Error object
						$this->errors->add( "missing_config_key_{$key}", sprintf( eac_attr__( "%s is missing in the configuration array", 'exopite-sof' ), $key ) );
					}

				endforeach;

				$errors_array = $this->errors->get_error_messages();

				// if the errors are logged, add the admin display hook
				if ( ! empty( $errors_array ) ) {
					add_action( 'admin_notices', array( $this, 'display_admin_error' ) );
				}

			} // ! empty( $required_key_array )

		} //check_required_keys()

		/**
		 * Set Properties of the class
		 */
		protected function set_properties() {

			if ( isset( $this->config['type'] ) ) {
				$this->set_type( $this->config['type'] );
			}

			// Parse the configuration against default values for Menu
			if ( $this->is_menu() ) {
				$default_menu_config = $this->get_config_default_menu();
				$this->config        = wp_parse_args( $this->config, $default_menu_config );

				// override option type to nullify 'simple' even if added
				$this->config['options'] = ''; // so, even if options is 'simple', we make it non-simple

			}

			// Parse the configuration against default values for Metabox
			if ( $this->is_metabox() ) {
				$default_metabox_config = $this->get_config_default_metabox();
				$this->config           = wp_parse_args( $this->config, $default_metabox_config );

				// This override s done for testing active plugin for qTranX and Wp-Multilang
				if ( $this->is_special_multilang_active() ) {

					$this->config['multilang'] = true; // so, even if multilingual was TRUE, we make it FALSE
					$this->config['options']   = false; // We can only save data for special languages in non-simple options

				} else {
					// override multilang true so that we dont save meta for language
					$this->config['multilang'] = false; // so, even if multilingual was TRUE, we make it FALSE

				}

			}

			$this->set_is_multilang_from_config();

			$this->config['is_options_simple'] = ( $this->is_options_simple() ) ? true : false;

			$this->dirname = wp_normalize_path( dirname( __FILE__ ) );
		}

		public function is_special_multilang_active() {

			return Exopite_Simple_Options_Framework_Helper::is_special_multilang_plugin_active();

		}

		public function display_error() {
			add_action( 'admin_notices', array( $this, 'display_admin_error' ) );
		}

		public function display_admin_error() {

			$class        = 'notice notice-error';
			$message      = '';
			$errors_array = $this->errors->get_error_messages();


			if ( ! empty( $errors_array ) ) {
				// Get the error messages from the array
				$message .= esc_html( implode( ', ', $errors_array ) );
			} else {
				// if no message is set, throw generic error message
				$message .= eac_attr__( 'Irks! An un-known error has occurred.', 'exopite-sof' );
			}

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		}

		/**
		 * Register all of the hooks shared by all $type  metabox | menu
		 */
		protected function define_shared_hooks() {

			// Upload hooks are only required for both,
			Exopite_Simple_Options_Framework_Upload::add_hooks();

			//scripts and styles
			add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts_styles' ) );

			/**
			 * Add "code" plugin for TinyMCE
			 * @link https://www.tinymce.com/docs/plugins/code/
			 */
			add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );

		}//define_shared_hooks()

		protected function define_hooks() {

			if ( $this->is_menu() ) {

				$this->define_menu_hooks();

			} elseif ( $this->is_metabox() ) {

				$this->define_metabox_hooks();

			}

		}

		/**
		 * Register all of the hooks related to 'menu' functionality
		 *
		 * @access   protected
		 */
		protected function define_menu_hooks() {

			/**
			 * Load options only if menu
			 * on metabox, page id is not yet available
			 */
			$this->db_options = apply_filters( 'exopite_sof_menu_get_options', get_option( $this->unique ), $this->unique );


			add_action( 'admin_init', array( $this, 'register_setting' ) );
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
			add_action( 'wp_ajax_exopite-sof-export-options', array( $this, 'export_options' ) );
			add_action( 'wp_ajax_exopite-sof-import-options', array( $this, 'import_options' ) );
			add_action( 'wp_ajax_exopite-sof-reset-options', array( $this, 'reset_options' ) );

			// if ( isset( $this->config['plugin_basename'] ) && ! empty( $this->config['plugin_basename'] ) ) {
			// 	add_filter( 'plugin_action_links_' . $this->config['plugin_basename'], array(
			// 		$this,
			// 		'plugin_action_links'
			// 	) );
			// }

		}

		/**
		 * Register all of the hooks related to 'metabox' functionality
		 *
		 * @access   protected
		 */
		protected function define_metabox_hooks() {

			/**
			 * Add metabox and register custom fields
			 *
			 * @link https://code.tutsplus.com/articles/rock-solid-wordpress-30-themes-using-custom-post-types--net-12093
			 */
			add_action( 'admin_init', array( $this, 'add_meta_box' ) );
			add_action( 'save_post', array( $this, 'save' ) );

		}

		/**
		 * Sets the $type property
		 *
		 * @param string $config_type
		 */
		protected function set_type( $config_type ) {

			$config_type = sanitize_key( $config_type );

			switch ( $config_type ) {
				case ( 'menu' ):
					$this->type = 'menu';
					break;

				case ( 'metabox' ):
					$this->type = 'metabox';
					break;

				default:
					$this->type = '';
			}

		}

		/**
		 * @return bool true if its a metabox type
		 */
		protected function is_metabox() {

			return ( $this->type === 'metabox' ) ? true : false;
		}

		/**
		 * @return bool true if its a metabox type
		 */
		protected function is_menu() {

			return ( $this->type === 'menu' ) ? true : false;
		}

		/**
		 * @return bool true if its a 'options'   => 'simple' in $config
		 */
		protected function is_options_simple() {

			// 'options'    => 'simple' in $config only required for metabox type
			return ( ! $this->is_menu() && isset( $this->config['options'] ) && $this->config['options'] === 'simple' ) ? true : false;
		}

		protected function set_is_multilang_from_config() {

			/**
			 * We need to store the value from $config to a property, so that we can keep track of the actual input
			 */

			/**
			 * Make sure if metabox and simple options are activated,
			 * then multilang is disabled.
			 * Enabled multilang is only required for qTranslate-X.
			 */
			if ( $this->is_metabox() && $this->is_options_simple() ) {
				$this->is_multilang = $this->config['is_multilang'] = false;
			}

			$this->is_multilang = $this->config['is_multilang'] = isset( $this->config['multilang'] ) && ( $this->config['multilang'] === true ) ? true : false;

		}

		/**
		 * @return bool true if multilang is set to true
		 */
		protected function is_multilang() {

			return $this->is_multilang;

		}

		/**
		 * @return bool true if its menu options
		 */
		protected function is_menu_page_loaded() {

			$current_screen = get_current_screen();

			return substr( $current_screen->id, - strlen( $this->unique ) ) === $this->unique;

		}

		/**
		 * check if the admin screen is of the post_type defined in config
		 * @return bool true if its menu options
		 */
		protected function is_metabox_enabled_post_type() {

			if ( ! isset( $this->config['post_types'] ) ) {
				return false;
			}

			$current_screen = get_current_screen();

			$post_type_loaded = $current_screen->id;

			return ( in_array( $post_type_loaded, $this->config['post_types'] ) ) ? true : false;

		}


		// for TinyMCE Code Plugin
		public function mce_external_plugins( $plugins ) {
			$url             = $this->get_url( $this->dirname );
			$base            = trailingslashit( join( '/', array( $url, 'assets' ) ) );
			$plugins['code'] = $base . 'plugin.code.min.js';

			return $plugins;
		}

		public function import_options() {

			$retval = 'error';
			
			$wpnonce = (isset($_POST['wpnonce'])) ? wp_unslash($_POST['wpnonce']) : '';
			$unique = (isset($_POST['unique'])) ? sanitize_key(wp_unslash($_POST['unique'])) : '';
			$value = (isset($_POST['value'])) ? stripslashes(wp_unslash($_POST['value'])) : '';

			if ( wp_verify_nonce( $wpnonce, 'exopite_sof_backup' ) && ! empty( $unique ) && ! empty( $value ) ) {
				
				$value = json_decode( $value, true );

				if ( is_array( $value ) ) {

					update_option( $unique, $value );
					$retval = 'success';

				}

			}

			die( $retval );

		}

		public function export_options() {
			
			$export = (isset($_GET['export'])) ? sanitize_key(wp_unslash($_GET['export'])) : '';
			$wpnonce = (isset($_GET['wpnonce'])) ? wp_unslash($_GET['wpnonce']) : '';
			if ( $export && wp_verify_nonce( $wpnonce, 'exopite_sof_backup' ) ) {

				header( 'Content-Type: plain/text' );
				header( 'Content-disposition: attachment; filename=exopite-sof-options-' . gmdate( 'd-m-Y' ) . '.txt' );
				header( 'Content-Transfer-Encoding: binary' );
				header( 'Pragma: no-cache' );
				header( 'Expires: 0' );

				// Using json_encode()
				echo json_encode( get_option( $export ) );

			}

			die();
		}

		public function reset_options() {

			$retval = 'error';

			$wpnonce = (isset($_POST['wpnonce'])) ? wp_unslash($_POST['wpnonce']) : '';
			$unique = (isset($_POST['unique'])) ? sanitize_key(wp_unslash($_POST['unique'])) : '';
			
			if ( wp_verify_nonce( $wpnonce, 'exopite_sof_backup' ) && ! empty($unique) ) {

				delete_option( $unique );

				$retval = 'success';

			}

			die( $retval );
		}

		/**
		 * Load classes
		 */
		public function load_classes() {

			require_once 'multilang-class.php';
			require_once 'fields-class.php';
			require_once 'upload-class.php';
			require_once 'sanitize-class.php';

		}

		/**
		 * Get url from path
		 * works only for local urls
		 *
		 * @param  string $path the path
		 *
		 * @return string   the generated url
		 */
		public function get_url( $path = '' ) {

			$url = str_replace(
				wp_normalize_path( untrailingslashit( ABSPATH ) ),
				site_url(),
				$path
			);

			return $url;
		}

		public function locate_template( $type ) {

			/**
			 * Ideas:
			 * - May extend this with override.
			 */
			// This should be the name of directory in theme/ child theme
			$override_dir_name = 'exopite';
			$fields_dir_name   = 'fields';

			$template = join( DIRECTORY_SEPARATOR, array( $this->dirname, $fields_dir_name, $type . '.php' ) );

			return $template;

		}

		/**
		 * Register "settings" for plugin option page in plugins list
		 *
		 * @param array $links plugin links
		 *
		 * @return array possibly modified $links
		 */
		public function plugin_action_links( $links ) {
			/**
			 *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
			 */

			// BOOL of settings is given true | false
			if ( is_bool( $this->config['settings_link'] ) ) {

				// FALSE: If it is false, no need to go further
				if ( ! $this->config['settings_link'] ) {
					return $links;
				}

				// TRUE: if Settings link is not defined, lets create one
				if ( $this->config['settings_link'] ) {

					$options_base_file_name = sanitize_file_name( $this->config['parent'] );

					$options_page_id = $this->unique;

					$settings_link = "{$options_base_file_name}?page={$options_page_id}";

					$settings_link_array = array(
						'<a href="' . admin_url( $settings_link ) . '">' . __( '设置', '' ) . '</a>',
					);

					return array_merge( $settings_link_array, $links );
				}
			} // if ( is_bool( $this->config['settings_link'] ) )

			// URL of settings is given
			if ( ! is_bool( $this->config['settings_link'] ) && ! is_array( $this->config['settings_link'] ) ) {
				$settings_link = esc_url( $this->config['settings_link'] );

				return array_merge( $settings_link, $links );
			}

			// Array of settings_link is given
			if ( is_array( $this->config['settings_link'] ) ) {


				$settings_links_config_array = $this->config['settings_link'];
				$settings_link_array         = array();

				foreach ( $settings_links_config_array as $link ) {

					$link_text         = isset( $link['text'] ) ? sanitize_text_field( $link['text'] ) : __( 'Settings', '' );
					$link_url_un_clean = isset( $link['url'] ) ? $link['url'] : '#';

					$link_type = isset( $link['type'] ) ? sanitize_key( $link['type'] ) : 'default';

					switch ( $link_type ) {
						case ( 'external' ):
							$link_url = esc_url_raw( $link_url_un_clean );
							break;

						case ( 'file' ):
							$link_url = admin_url( sanitize_file_name( $link_url_un_clean ) );
							break;

						default:

							if ( $this->config['submenu'] ) {

								$options_base_file_name = sanitize_file_name( $this->config['parent'] );

								$options_base_file_name_extension = pathinfo( parse_url( $options_base_file_name )['path'], PATHINFO_EXTENSION );

								if ( $options_base_file_name_extension === 'php' ) {
									$options_base = $options_base_file_name;
								} else {
									$options_base = 'admin.php';
								}
								$options_page_id = $this->unique;

								$settings_link = "{$options_base}?page={$options_page_id}";

								$link_url = admin_url( $settings_link );

							} else {
								$settings_link = "?page={$this->unique}";
								$link_url      = admin_url( $settings_link );
							}

					}

					$settings_link_array[] = '<a href="' . $link_url . '">' . $link_text . '</a>';

				}

				return array_merge( $settings_link_array, $links );

			} // if ( is_array( $this->config['settings_link'] ) )

			// if nothing is returned so far, return original $links
			return $links;

		}

		/**
		 * Get default config for menu
		 * @return array $default
		 */
		public function get_config_default_menu() {

			$default = array(
				//
				'parent'        => 'options-general.php',
				'menu'          => 'plugins.php', // For backward compatibility
				'menu_title'    => __( '酱茄Free小程序', 'exopite-options-framework' ),
				// Required for submenu
				'submenu'       => false,
				//The name of this page
				'title'         => __( 'Plugin Options', 'exopite-options-framework' ),
				// The capability needed to view the page
				'capability'    => 'manage_options',
				'settings_link' => true,
				'tabbed'        => true,
				'position'      => 2,
				'icon'          => '',
				'multilang'     => true,
				'options'       => false
			);

			return apply_filters( 'exopite_sof_filter_config_default_menu_array', $default );
		}

		/**
		 * Get default config for metabox
		 * @return array $default
		 */
		public function get_config_default_metabox() {

			$default = array(

				'title'      => '',
				'post_types' => array( 'post' ),
				'context'    => 'advanced',
				'priority'   => 'default',
				'capability' => 'edit_posts',
				'tabbed'     => true,
				'options'    => false,
				'multilang'  => false

			);

			return apply_filters( 'exopite_sof_filter_config_default_metabox_array', $default );
		}

		/* Create a meta box for our custom fields */
		public function add_meta_box() {

			add_meta_box(
				$this->unique,
				$this->config['title'],
				array( $this, 'display_page' ),
				$this->config['post_types'],
				$this->config['context'],
				$this->config['priority']
			);

		}

		/**
		 * Register settings for plugin option page with a callback to save
		 */
		public function register_setting() {

			register_setting( $this->unique, $this->unique, array( $this, 'save' ) );

		}

		/**
		 * Register plugin option page
		 */
		public function add_admin_menu() {

			// Is it a main menu or sub_menu
			if ( ! (bool) $this->config['submenu'] ) {

//				$default['icon']     = '';
//				$default['position'] = 100;
//				$default['menu']     = 'Plugin menu';

//				$this->config = wp_parse_args( $this->config, $default );

				$menu = add_menu_page(
					$this->config['title'],
					$this->config['menu_title'],
					$this->config['capability'],
					$this->unique, //slug
					array( $this, 'display_page' ),
					$this->config['icon'],
					$this->config['position']
				);

			} else {

//				$this->config = wp_parse_args( $this->config, $default );

				$submenu = add_submenu_page(
					$this->config['parent'],
					$this->config['title'],
					$this->config['title'],
					$this->config['capability'],
					$this->unique, // slug
					array( $this, 'display_page' )
				);

			}

		}

		/**
		 * Load scripts and styles
		 *
		 * @hooked  admin_enqueue_scripts
		 *
		 * @param string hook name
		 */
		public function load_scripts_styles( $hook ) {

			// return if not admin
			if ( ! is_admin() ) {
				return;
			}

			/**
			 * Load Scripts for only Menu page
			 */
			if ( $this->is_menu_page_loaded() ):

			endif; //$this->is_menu_page_loaded()


			/**
			 * Load Scripts for metabox that have enabled metabox using Exopite framework
			 */
			if ( $this->is_metabox_enabled_post_type() ):

			endif; // $this->is_metabox_enabled_post_type()


			/**
			 * Load Scripts shared by all $type
			 */
			if ( $this->is_menu_page_loaded() || $this->is_metabox_enabled_post_type() ) :

				$url  = $this->get_url( $this->dirname );
				$base = trailingslashit( join( '/', array( $url, 'assets' ) ) );

				if ( ! wp_style_is( 'font-awesome' ) || ! wp_style_is( 'font-awesome-470' ) || ! wp_style_is( 'FontAwesome' ) ) {

					/* Get font awsome */
					wp_enqueue_style( 'font-awesome-470', $base . 'font-awesome-4.7.0/font-awesome.min.css', array(), $this->version, 'all' );

				}

				/**
				 * jquery-ui-core is built into WordPress
				 *
				 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/
				 */
				wp_enqueue_style( 'jquery-ui-core' );
				// wp_enqueue_style( 'jquery-ui', $base . 'jquery-ui.css', array(), '1.8.24', 'all' );
				wp_enqueue_style( 'exopite-simple-options-framework', $base . 'styles.css', array(), $this->version, 'all' );

				// Add jQuery form scripts for menu options AJAX save
				wp_enqueue_script( 'jquery-form' );

				// Add the date picker script
				wp_enqueue_script( 'jquery-ui-datepicker' );

				// wp_enqueue_script( 'jquery-ui-sortable' );

				$scripts_styles = array(
					array(
						'name' => 'jquery-interdependencies',
						'fn'   => 'jquery.interdependencies.min.js',
						'dep'  => array(
							'jquery',
							'jquery-ui-datepicker',
							'wp-color-picker'
						),
					),
					array(
						'name' => 'jquery.sortable',
						'fn'   => 'html5sortable.min.js',
						'dep'  => array(
							'jquery',
						),
					),
					array(
						'name' => 'exopite-simple-options-framework-js',
						'fn'   => 'scripts.min.js',
						'dep'  => array(
							'jquery',
							'jquery-ui-datepicker',
							'wp-color-picker',
							'jquery-interdependencies'
						),
					),
				);

				foreach ( $scripts_styles as $item ) {
					wp_enqueue_script( $item['name'], $base . $item['fn'], $item['dep'], $this->version, true );
				}

				/**
				 * Enqueue class scripts
				 * with this, only enqueue scripts if class/field is used
				 */
				$this->enqueue_field_classes();

			endif; //$this->is_menu_page_loaded() || $this->is_metabox_enabled_post_type()

		}

		/**
		 * Save options or metabox to meta
		 *
		 * @return mixed
		 *
		 * @hooked  'save_post' for metabox
		 * @hooked  register_setting() for menu
		 *
		 */
		public function save( $posted_data ) {

			// Is user has ability to save?
			if ( ! current_user_can( $this->config['capability'] ) ) {
				return null;
			}

			$valid   = array();
			$post_id = null;

			/**
			 * @var $section_fields_with_values it will hold the sanitized fields => value
			 */
			$section_fields_with_values = array();

			// Specific to Menu Options
			if ( $this->is_menu() ) {

				/**
				 * Import options should not be checked.
				 */
				 $action = (isset($_POST['action'])) ? sanitize_key(wp_unslash($_POST['action'])) : '';
				if ( $action === 'exopite-sof-import-options' ) {
					return apply_filters( 'exopite_sof_import_options', $posted_data, $this->unique );
				}

				// Preserve values start with "_".
                $options = get_option( $this->unique );
                if ( is_array( $options ) ) {

                    foreach ( $options as $key => $value ) {

                        if ( substr( $key, 0, 1 ) === '_' ) {

                            $valid[ $key ] = $value;

                        }

                    }

                }

			}

			// Specific to Metabox
			if ( $this->is_metabox() ) {

				// if this is metabox, $posted_data is post_id we are saving
				$post_id = $posted_data;
				if ( $this->is_options_simple() ) {
					$posted_data = $_POST;
				} else {

					if ( isset( $_POST[ $this->unique ] ) ) {
						$posted_data = wp_unslash( $_POST[ $this->unique ] );
					} else {
						return false;
					}

				}

				if ( $posted_data === null ) return;

				// Stop WP from clearing custom fields on autosave
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
					return null;
				}

				// Prevent quick edit from clearing custom fields
				if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
					return null;
				}

				// If the post type is not in our post_type array, do nothing
				if ( ! in_array( get_post_type( $post_id ), $this->config['post_types'] ) ) {
					return null;
				}

			}

			/**
			 * This must be running on post meta too because of the qTranslate-X.
			 * Maybe check first if qTranslate-X is installed and active?
			 */
			// Preserve other language setting
			// Get current field value, make sure we are not override other language values

			if ( $this->is_multilang() ) {

				$other_languages = $this->languages_except_current_language();

				foreach ( $other_languages as $language ) {

					if ( $this->is_metabox() ) {
						// required for qTranslate-X and WP Multilang
						$post_meta = get_post_meta( $post_id );

						if ( ! empty( $post_meta ) ) {
							$options_array_from_post_meta = maybe_unserialize( $post_meta[ $this->unique ][0] );
							if ( is_array( $options_array_from_post_meta ) ) {
								$section_fields_with_values[ $language ] = $options_array_from_post_meta[ $language ];
							}
						}
					}

					if ( $this->is_menu() ) {
						$section_fields_with_values[ $language ] = $this->db_options[ $language ];
					}
				}
			}

			$sanitizer = new Exopite_Simple_Options_Framework_Sanitize( $this->is_multilang(), $this->lang_current, $this->config, $this->fields );
			if ( $this->is_multilang() ) {
				$section_fields_with_values[ $this->lang_current ] = $sanitizer->get_sanitized_values( $this->fields, $posted_data[$this->lang_current] );
			} else {
				$section_fields_with_values = $sanitizer->get_sanitized_values( $this->fields, $posted_data );
			}

			/**
			 * The idea here is that, this hook run on both.
			 */
			do_action( 'exopite_sof_do_save_options', $section_fields_with_values, $this->unique, $post_id );
			$valid = apply_filters( 'exopite_sof_save_options', $section_fields_with_values, $this->unique, $post_id );

			if ( $this->is_menu() ) {

				// These actions and filters only for options menu
				$valid = apply_filters( 'exopite_sof_save_menu_options', $section_fields_with_values, $this->unique );
				do_action( 'exopite_sof_do_save_menu_options', $section_fields_with_values, $this->unique );

				return $valid;
			}

			if ( $this->is_metabox() ) {

				// When we click on "New Post" (CPT), then $post is not available, so we need to check if it is set
				/**
				 * NEED TESTING!
				 * I'm not sure about this, need check. I think post ID IS available because saving custom field
				 * should be after post is inserted.
				 */
				if ( isset( $post_id ) ) {

					$valid = apply_filters( 'exopite_sof_save_meta_options', $section_fields_with_values, $this->unique, $post_id );
					do_action( 'exopite_sof_do_save_meta_options', $section_fields_with_values, $this->unique, $post_id );

					if ( $this->is_options_simple() ) {

						foreach ( $valid as $key => $value ) {

							update_post_meta( $post_id, $key, $value );

						}
					} else {
						update_post_meta( $post_id, $this->unique, $valid );
					}

				}

			}

			unset( $post_id, $valid, $posted_data, $section_fields_with_values, $section_fields_current_lang );

		}

		//DEGUB
		public function write_log( $type, $log_line ) {

			$hash        = '';
			$fn          = plugin_dir_path( __FILE__ ) . '/' . $type . $hash . '.log';
			$log_in_file = file_put_contents( $fn, date( 'Y-m-d H:i:s' ) . ' - ' . $log_line . PHP_EOL, FILE_APPEND );

		}

		/**
		 * Loop fileds based on field from user
		 *
		 * @param $callbacks
		 */
		public function loop_fields( $callbacks ) {

			if ( ! is_array( $this->fields ) ) return;

			foreach ( $this->fields as $section ) {

				// before
				if ( $callbacks['before'] ) {
					call_user_func( array( $this, $callbacks['before'] ), $section );
				}

				if ( ! isset( $section['fields'] ) || ! is_array( $section['fields'] ) ) {
					continue;
				}

				foreach ( $section['fields'] as $field ) {

					// If has subfields
					if ( ( $callbacks['main'] == 'include_field_class' || $callbacks['main'] == 'enqueue_field_class' ) && isset( $field['fields'] ) ) {

						foreach ( $field['fields'] as $subfield ) {

							if ( $callbacks['main'] ) {
								call_user_func( array( $this, $callbacks['main'] ), $subfield );
							}

						}

					}

					if ( $callbacks['main'] ) {
						call_user_func( array( $this, $callbacks['main'] ), $field );
					}

					// main

				}

				// after
				if ( $callbacks['after'] ) {
					call_user_func( array( $this, $callbacks['after'] ) );
				}
			}

		}

		/**
		 * @link https://thisinterestsme.com/php-using-recursion-print-values-multidimensional-array/
		 */
		public function recursive_walk( $array, &$fields ){

			foreach( $array as $key => $value ) {

				if ( is_array( $value ) ) {

					if ( isset( $value['type'] ) ) {

						if ( ! in_array( $value['type'], $fields ) && ! empty( $value['type'] ) ) {

							$temp_array = array(
								'type' 	=> $value['type'],
							);

							if ( isset( $value['id'] ) ) {
								$temp_array['id'] = $value['id'];
							}

							$fields[ $value['type'] ] = $temp_array;

						}

						if ( $value['type'] == 'editor' && isset( $value['editor'] ) ) {

							if ( ! isset( $fields[ $value['type'] ]['editor'] ) || ! is_array( $fields[ $value['type'] ]['editor'] ) ) {
								$fields[ $value['type'] ]['editor'] = array();
							}

							if ( ! in_array( $value['editor'], $fields[ $value['type'] ]['editor'] ) ) {
								$fields[ $value['type'] ]['editor'][] = $value['editor'];
							}

						}

					}

					$this->recursive_walk( $value, $fields );

				}

			}

			return $fields;

		}

		/**
		 * Loop and add callback to include and enqueue
		 */
		public function include_field_classes() {

			if ( empty( $this->fields ) ) {
				return;
			}

			$fields = array();
			$fields = $this->recursive_walk( $this->fields, $fields );

			foreach ( $fields as $field => $args ) {

				$this->include_field_class( $field );

			}

		}

		/**
		 * Loop and add callback to include and enqueue
		 */
		public function enqueue_field_classes() {

			if ( empty( $this->fields ) ) {
				return;
			}

			$fields = array();
			$fields = $this->recursive_walk( $this->fields, $fields );

			foreach ( $fields as $field => $args ) {

				$this->enqueue_field_class( $args );

			}

		}

		/**
		 * Include field classes
		 * and enqueue they scripts
		 */
		public function include_field_class( $field ) {

            if ( is_array( $field ) && isset( $field['type'] ) ) {
                $field = $field['type'];
            }

			$class = 'Exopite_Simple_Options_Framework_Field_' . $field;

			if ( ! class_exists( $class ) ) {

				$field_filename = $this->locate_template( $field );

				if ( file_exists( $field_filename ) ) {

					require_once join( DIRECTORY_SEPARATOR, array(
						$this->dirname,
						'fields',
						$field . '.php'
					) );

				}

			}

		}

		/**
		 * Include field classes
		 * and enqueue they scripts
		 */
		public function enqueue_field_class( $field ) {

			$class = 'Exopite_Simple_Options_Framework_Field_' . $field['type'];

			if ( class_exists( $class ) && method_exists( $class, 'enqueue' ) ) {

				$args = array(
					'plugin_sof_url'  => plugin_dir_url( __FILE__ ),
					'plugin_sof_path' => plugin_dir_path( __FILE__ ),
					'field'           => $field,
				);

				$class::enqueue( $args );

			}

		}

		/**
		 * Generate files
		 *
		 * @param  array $field field args
		 *
		 * @echo string   generated HTML for the field
		 */
		public function add_field( $field, $value = null ) {

			do_action( 'exopite_sof_before_generate_field', $field, $this->config );
			do_action( 'exopite_sof_before_add_field', $field, $this->config );

			$output     = '';
			$class      = 'Exopite_Simple_Options_Framework_Field_' . $field['type'];
			$depend     = '';
			$wrap_class = ( ! empty( $field['wrap_class'] ) ) ? ' ' . $field['wrap_class'] : '';
			$hidden     = ( $field['type'] == 'hidden' ) ? ' hidden' : '';
			$sub        = ( ! empty( $field['sub'] ) ) ? 'sub-' : '';

			/**
			 * Add editor name to classes for styling purposes.
			 */
			if (  $field['type'] == 'editor' ) {

				if ( ! isset( $field['editor'] ) || $field['editor'] == 'tinymce' ) {
					$wrap_class .= ' exopite-sof-tinymce-editor';
				} elseif ( isset( $field['editor'] ) && $field['editor'] == 'trumbowyg' ) {
					$wrap_class .= ' exopite-sof-trumbowyg-editor';
				}

			}

			if ( ! empty( $field['dependency'] ) ) {
				$hidden = ' hidden';
				$depend .= ' data-' . $sub . 'controller="' . $field['dependency'][0] . '"';
				$depend .= ' data-' . $sub . 'condition="' . $field['dependency'][1] . '"';
				$depend .= ' data-' . $sub . 'value="' . $field['dependency'][2] . '"';
			}

			if ( ! isset( $field['pseudo'] ) || ! $field['pseudo'] ) {
				$output .= '<div class="exopite-sof-field exopite-sof-field-' . $field['type'] . $wrap_class . $hidden . '"' . $depend . '>';
			}

			if ( isset( $field['title'] ) ) {

				$output .= '<h4 class="exopite-sof-title">';

				$output .= $field['title'];

				if ( ! empty( $field['description'] ) ) {
					$output .= '<p class="exopite-sof-description">' . $field['description'] . '</p>';
				}

				$output .= '</h4>'; // exopite-sof-title
				$output .= '<div class="exopite-sof-fieldset">';
			}

			if ( class_exists( $class ) ) {

				if ( empty( $value ) && $value !== 0 && $value !== '0' ) {

					// NEW

					if ( ( isset( $field['sub'] ) && ! empty( $field['sub'] ) ) || $this->is_menu() ) {
						$value = $this->get_value( $this->db_options, $field  );
					}

					if ( $this->is_metabox() ) {

						if ( $this->is_options_simple() ) {

							$meta = get_post_meta( get_the_ID() );

							/**
							 * get_post_meta return empty on non existing meta
							 * we need to check if meta key is exist to return null,
							 * because default value can only display if value is null
							 * on empty vlaue - may saved by the user - should display empty.
							 */
							if ( isset( $field['id'] ) && isset( $meta[ $field['id'] ] ) ) {

								$value = array_shift( $meta[ $field['id'] ] );

							} else {
								$value = null;
							}

						} else {

							$meta  = get_post_meta( get_the_ID(), $this->unique, true );
							$value = $this->get_value( $meta, $field  );

						}

					}

				}

				ob_start();
				$element = new $class( $field, $value, $this->unique, $this->config, $this->multilang );
				$element->output();
				$output .= ob_get_clean();

			} else {

				$output .= '<div class="danger unknown">';
				$output .= esc_attr__( 'ERROR:', 'exopite-simple-options' ) . ' ';
				$output .= esc_attr__( 'This field class is not available!', 'exopite-simple-options' );
				$output .= ' <i>(' . $field['type'] . ')</i>';
				$output .= '</div>';

			}

			if ( isset( $field['title'] ) ) {
				$output .= '</div>';
			} // exopite-sof-fieldset

			if ( ! isset( $field['pseudo'] ) || ! $field['pseudo'] ) {
				$output .= '<div class="clearfix"></div>';
				$output .= '</div>'; // exopite-sof-field
			}

			do_action( 'exopite_sof_after_generate_field', $field, $this->config );

			echo apply_filters( 'exopite_sof_add_field', $output, $field, $this->config );

			do_action( 'exopite_sof_after_add_field', $field, $this->config );

		}

		public function get_value( $options, $field  ) {

			/**
			 * IF MULTILANG
			 * - get option[current-lang][id]
			 * - but not the get option[default-lang][id] <- because if no options in current lang, should display default
			 * NOT MULTILANG OR MULTILANG NOT EXIST THEN TRY
			 * - get option[id]
			 */

			$value = null;

			if ( ! isset( $field['id'] ) ) {
				return $value;
			}

			if ( isset( $options[ $this->lang_current ][ $field['id'] ] ) ) {

				$value = $options[ $this->lang_current ][ $field['id'] ];

			} elseif ( $value === null && isset( $options[ $field['id'] ] ) ) {

				$value = $options[ $field['id'] ];

			}

			/**
			 * Use this filter, like:
			 *
			 * add_filter( 'exopite_sof_field_value', 'prefix_exopite_sof_field_value', 10, 5 );
			 * public function prefix_exopite_sof_field_value( $value, $unique, $options, $field ) {
			 *
			 *	   if ( $unique == $this->jiangqie_api && $field['id'] == 'your-field-id' ) {
			 *		   // do the magic ;)
			 *	   }
			 *
			 *	   return $value;
			 * }
			 */
			return apply_filters( 'exopite_sof_field_value', $value, $this->unique, $options, $field );

		}

		/**
		 * Display form and header for options page
		 * for metabox no need to do this.
		 */
		public function display_options_page_header() {

			echo '<form method="post" action="options.php" enctype="multipart/form-data" name="' . $this->unique . '" class="exopite-sof-form-js ' . $this->unique . '-form" data-save="' . esc_attr__( 'Saving...', 'exopite-sof' ) . '" data-saved="' . esc_attr__( 'Saved Successfully.', 'exopite-sof' ) . '">';

			settings_fields( $this->unique );
			do_settings_sections( $this->unique );
			$current_language_title = '';
			if ( $this->is_multilang() ) {
				$current_language_title = apply_filters( 'exopite_sof_title_language_notice', $this->lang_current );
				$current_language_title = ' [ ' . $current_language_title . ' ]';
			}

			echo '<header class="exopite-sof-header exopite-sof-header-js">';
			echo '<h1>' . $this->config['title'] . $current_language_title . '</h1>';

			echo '<span class="exopite-sof-search-wrapper"><input type="text" class="exopite-sof-search"></span>';

			echo '<fieldset><span class="exopite-sof-ajax-message"></span>';
			submit_button( esc_attr__( '保存设置', 'exopite-sof' ), 'primary ' . 'exopite-sof-submit-button-js', $this->unique . '-save', false, array() );
			echo '</fieldset>';
			echo '</header>';

		}

		/**
		 * Display form and footer for options page
		 * for metabox no need to do this.
		 */
		public function display_options_page_footer() {

			echo '<footer class="exopite-sof-footer-js exopite-sof-footer">';

			echo '<fieldset><span class="exopite-sof-ajax-message"></span>';
			submit_button( esc_attr__( '保存设置', 'exopite-sof' ), 'primary ' . 'exopite-sof-submit-button-js', '', false, array() );
			echo '</fieldset>';

			echo '</footer>';

			echo '</form>';

		}

		/**
		 * Display section header, only first is visible on start
		 */
		public function display_options_section_header( $section ) {

			$visibility = ' hide';
			if ( $section === reset( $this->fields ) ) {
				$visibility = '';
			}

            $section_name = ( isset( $section['name'] ) ) ? $section['name'] : '';
            $section_icon = ( isset( $section['icon'] ) ) ? $section['icon'] : '';

			echo '<div class="exopite-sof-section exopite-sof-section-' . $section_name . $visibility . '">';

			if ( isset( $section['title'] ) && ! empty( $section['title'] ) ) {

				$icon_before = '';
				if ( strpos( $section_icon, 'dashicon' ) !== false ) {
					$icon_before = 'dashicons-before ';
				} elseif ( strpos( $section_icon, 'fa' ) !== false ) {
					$icon_before = 'fa-before ';
				}

				echo '<h2 class="exopite-sof-section-header" data-section="' . $section_name . '"><span class="' . $icon_before . $section_icon . '"></span>' . $section['title'] . '</h2>';

			}

		}

		/**
		 * Display section footer
		 */
		public function display_options_section_footer() {

			echo '</div>'; // exopite-sof-section

		}

		public function get_fields() {

			if ( ! $this->elements ) return;

			$fields = array();

			foreach ( $this->elements as $key => $value ) {

				if( isset( $value['sections'] ) ) {

					foreach ( $value['sections'] as $section ) {

						if( isset( $section['fields'] ) ) {
							$fields[] = $section;
						}

					}

				} else {

					if( isset( $value['fields'] ) ) {
						$fields[] = $value;
					}

				}

			}

			$this->fields = $fields;
		}

		public function get_menu_item_icons( $section ) {

			if ( strpos( $section['icon'], 'dashicon' ) !== false ) {
				echo '<span class="exopite-sof-nav-icon dashicons-before ' . $section['icon'] . '"></span>';
			} elseif ( strpos( $section['icon'], 'fa' ) !== false ) {
				echo '<span class="exopite-sof-nav-icon fa-before ' . $section['icon'] . '" aria-hidden="true"></span>';
			}

		}

		public function get_menu_item( $section, $active = '', $force_hidden ) {

			// $active = '';
			// if ( $section === reset( $this->fields ) ) {
			// 	$active = ' active';
			// }

			$depend = '';
			$hidden = ( $force_hidden ) ? ' hidden' : '';

			// Dependency for tabs too
			if ( ! empty( $section['dependency'] ) ) {
				$hidden = ' hidden';
				$depend = ' data-controller="' . $section['dependency'][0] . '"';
				$depend .= ' data-condition="' . $section['dependency'][1] . '"';
				$depend .= ' data-value="' . $section['dependency'][2] . '"';
			}

			echo '<li  class="exopite-sof-nav-list-item' . $active . $hidden . '"' . $depend . ' data-section="' . $section['name'] . '">';
			echo '<span class="exopite-sof-nav-list-item-title">';
			$this->get_menu_item_icons( $section );
			echo $section['title'];
			echo '</span>';
			echo '</li>';

		}

		public function get_menu() {

			// $fields = array();

			echo '<div class="exopite-sof-nav"><ul class="exopite-sof-nav-list">';

			foreach ( $this->elements as $key => $value ) {

				$active = '';
				reset( $this->elements );
				if ( $key === key($this->elements ) ) {
					$active = ' active';
				}


				if( isset( $value['sections'] ) ) {

					echo '<li  class="exopite-sof-nav-list-parent-item' . $active . '">';
					echo '<span class="exopite-sof-nav-list-item-title">';
					$this->get_menu_item_icons( $value );
					echo $value['title'];
					echo '</span>';
					echo '<ul style="display:none;">';

					foreach ( $value['sections'] as $section ) {

						if( isset( $section['fields'] ) ) {

							$this->get_menu_item( $section, $active, false );

						}

					}

					echo '</ul>';
					echo '</li>';

				} else {

					if( isset( $value['fields'] ) ) {

						$this->get_menu_item( $value, $active, false );

					}

				}

			}

			echo '</ul></div>';

		}

		public function display_debug_infos() {

			echo '<pre>MULTILANG<br>';
			var_export( $this->config['multilang'] );
			echo '</pre>';

			echo '<pre>IS_SIMPLE:<br>';
			var_export( $this->is_options_simple() );
			echo '</pre>';

			echo '<pre>IS_MULTILANG<br>';
			var_export( $this->is_multilang() );
			echo '</pre>';

			echo '<pre>IS_SPECIAL_MULTILANG_PLUGIN_ACTIVE<br>';
			var_export( $this->is_special_multilang_active() );
			echo '</pre>';

			echo '<pre>DB_OPTIONS<br>';
			var_export( $this->db_options );
			echo '</pre>';

		}

		/**
		 * Display form form either options page or metabox
		 */
		public function display_page() {

			do_action( 'exopite_simple_options_framework_form_' . $this->config['type'] . '_before' );

			settings_errors();

			echo '<div class="exopite-sof-wrapper exopite-sof-wrapper-' . $this->config['type'] . ' ' . $this->unique . '-options">';

			switch ( $this->config['type'] ) {
				case 'menu':
					add_action( 'exopite_sof_display_page_header', array(
						$this,
						'display_options_page_header'
					), 10, 1 );
					do_action( 'exopite_sof_display_page_header', $this->config );
					break;

				case 'metabox':
					/**
					 * Get options
					 * Can not get options in __consturct, because there, the_ID is not yet available.
					 *
					 * Only if options is not simple, if yes, then value determinated when field are displayed.
					 * Simple options is stored az induvidual meta key, value pair, otherwise it is stored in an array.
					 *
					 * I implemented this option because it is possible to search in serialized (array) post meta:
					 * @link https://wordpress.stackexchange.com/questions/16709/meta-query-with-meta-values-as-serialize-arrays
					 * @link https://stackoverflow.com/questions/15056407/wordpress-search-serialized-meta-data-with-custom-query
					 * @link https://www.simonbattersby.com/blog/2013/03/querying-wordpress-serialized-custom-post-data/
					 *
					 * but there is no way to sort them with wp_query or SQL.
					 * @link https://wordpress.stackexchange.com/questions/87265/order-by-meta-value-serialized-array/87268#87268
					 * "Not in any reliable way. You can certainly ORDER BY that value but the sorting will use the whole serialized string,
					 * which will give * you technically accurate results but not the results you want. You can't extract part of the string
					 * for sorting within the query itself. Even if you wrote raw SQL, which would give you access to database functions like
					 * SUBSTRING, I can't think of a dependable way to do it. You'd need a MySQL function that would unserialize the value--
					 * you'd have to write it yourself.
					 * Basically, if you need to sort on a meta_value you can't store it serialized. Sorry."
					 *
					 * It is possible to get all required posts and store them in an array and then sort them as an array,
					 * but what if you want multiple keys/value pair to be sorted?
					 *
					 * UPDATE
					 * it is maybe possible:
					 * @link http://www.russellengland.com/2012/07/how-to-unserialize-data-using-mysql.html
					 * but it is waaay more complicated and less documented as meta query sort and search.
					 * It should be not an excuse to use it, but it is not as reliable as it should be.
					 *
					 * @link https://wpquestions.com/Order_by_meta_key_where_value_is_serialized/7908
					 * "...meta info serialized is not a good idea. But you really are going to lose the ability to query your
					 * data in any efficient manner when serializing entries into the WP database.
					 *
					 * The overall performance saving and gain you think you are achieving by serialization is not going to be noticeable to
					 * any major extent. You might obtain a slightly smaller database size but the cost of SQL transactions is going to be
					 * heavy if you ever query those fields and try to compare them in any useful, meaningful manner.
					 *
					 * Instead, save serialization for data that you do not intend to query in that nature, but instead would only access in
					 * a passive fashion by the direct WP API call get_post_meta() - from that function you can unpack a serialized entry
					 * to access its array properties too."
					 */
					if ( $this->config['type'] == 'metabox' && ( ! isset( $this->config['options'] ) || $this->config['options'] != 'simple' ) ) {
						$meta_options     = get_post_meta( get_the_ID(), $this->unique, true );
						$this->db_options = apply_filters( 'exopite-simple-options-framework-meta-get-options', $meta_options, $this->unique, get_the_ID() );
					}

					if ( $this->debug ) {
						$meta_options = get_post_meta( get_the_ID() );
						echo '<pre>POST_META<br>';
						var_export( $meta_options );
						echo '</pre>';
					}

					break;
			}

			if ( isset( $this->config['multilang'] ) && is_array( $this->config['multilang'] ) ) {

				switch ( $this->config['type'] ) {
					case 'menu':
						$current_language = $this->config['multilang']['current'];
						break;

					case 'metabox':
						// Pages/Posts can not have "All languages" displayed, then default will be displayed
						$current_language = ( $this->config['multilang']['current'] == 'all' ) ? $this->config['multilang']['default'] : $this->config['multilang']['current'];
						break;
				}

				/**
				 * ToDos:
				 * - check this value, shouldn't be hard coded.
				 */
				$current_language = 'en';
				/**
				 * Current language need to pass to save function, if "All languages" seleted, WPML report default
				 * on save hook.
				 */
				echo '<input type="hidden" name="_language" value="' . $current_language . '">';
			}

			$sections = count( $this->fields );

			$tabbed = ( $sections > 1 && $this->config['tabbed'] ) ? ' exopite-sof-content-nav exopite-sof-content-js' : '';

			if ( $this->debug ) {

				$this->display_debug_infos();

			}

			/**
			 * Generate fields
			 */
			// Generate tab navigation
			echo '<div class="exopite-sof-content' . $tabbed . '">';

			if ( ! empty( $tabbed ) ) {

				$this->get_menu();

			}

			echo '<div class="exopite-sof-sections">';

			// Generate fields
			$callbacks = array(
				'before' => 'display_options_section_header',
				'main'   => 'add_field',
				'after'  => 'display_options_section_footer'
			);

			$this->loop_fields( $callbacks );

			echo '</div>'; // sections
			echo '</div>'; // content
			if ( $this->config['type'] == 'menu' ) {

				add_action( 'exopite_sof_display_page_footer', array(
					$this,
					'display_options_page_footer'
				), 10, 1 );
				do_action( 'exopite_sof_display_page_footer', $this->config );

			}

			echo '</div>';

			do_action( 'exopite_sof_form_' . $this->config['type'] . '_after' );

		} // display_page()


	} //class

endif;

/**
 * Use:
 *  $options = ( function_exists( 'get_exopite_sof_option' ) ) ? get_exopite_sof_option( $this->jiangqie_api ) : get_option( $this->jiangqie_api );

 *  // OR
 *
 *  if ( function_exists( 'get_exopite_sof_option' ) ) {
 *      $options = get_exopite_sof_option( $this->jiangqie_api );
 *  } else {
 *      $options = get_option( $this->jiangqie_api );
 *  }
 */
if ( ! function_exists( 'get_exopite_sof_option' ) ) {
	function get_exopite_sof_option( $option_slug, $default = false  ) {

		/**
		 * save in cache?
		 * @link https://codex.wordpress.org/Transients_API
		 */

		if ( ! class_exists( 'Exopite_Simple_Options_Framework_Helper' ) ) {
			require_once 'multilang-class.php';
		}

		$language_defaults = Exopite_Simple_Options_Framework_Helper::get_language_defaults();

		$options = apply_filters( 'get_exopite_sof_option', get_option( $option_slug, $default ), $option_slug, $default );

		if ( isset( $options[$language_defaults['current']] ) ) {

			return $options[$language_defaults['current']];

		} elseif ( isset( $options[$language_defaults['default']] ) ) {

			return $options[$language_defaults['default']];

		} else {

			return $options;

		}

	}
}
