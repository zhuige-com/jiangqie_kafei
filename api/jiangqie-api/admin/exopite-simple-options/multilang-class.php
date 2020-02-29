<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Helper Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Helper' ) ) {

	class Exopite_Simple_Options_Framework_Helper {


		public static function get_active_multilang_plugins() {

			// Including file library if not exist
			if ( ! function_exists( 'is_plugin_active' ) ) {

				require_once ABSPATH . 'wp-admin/includes/plugin.php';

			}

			$language_plugins_active = array();

			// List Plugins in priority order

			$language_plugins_active[] = ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && class_exists( 'SitePress' ) ) ? 'wpml' : false;
			// polylang : https://wordpress.org/plugins/polylang/
			$language_plugins_active[] = ( is_plugin_active( 'polylang/polylang.php' ) && class_exists( 'Polylang' ) ) ? 'polylang' : false;
			// qTranX : https://wordpress.org/plugins/qtranslate-x/
			$language_plugins_active[] = ( is_plugin_active( 'qtranslate-x/qtranslate.php' ) && function_exists( 'qtranxf_getSortedLanguages' ) ) ? 'qtran' : false;
			// Wp Multilang : https://wordpress.org/plugins/wp-multilang/
			$language_plugins_active[] = ( is_plugin_active( 'wp-multilang/wp-multilang.php' ) && function_exists( 'wpm_get_user_language' ) ) ? 'wpm' : false;

			return apply_filters( 'exopite_sof_active_multilang_plugins', $language_plugins_active );

		}


		public static function is_special_multilang_plugin_active() {

			$language_plugins_active = self::get_active_multilang_plugins();

			if ( ! empty( $language_plugins_active ) ) {

				$special_plugins = apply_filters( 'exopite_sof_special_multilang_plugins', array(
					'qtran', // for qtranslateX
					'wpm' // wp-multilang
				) );


				foreach ( $special_plugins as $special_plugin ) {
					if ( in_array( $special_plugin, $language_plugins_active ) ) {
						return true;
					}
				}
			}


			return false;


		}


		/**
		 * Get language defaults
		 *
		 * ToDos:
		 * - add options to disable multilang
		 * - automatically save in value[current] also if no multilang plugin installed
		 *   this case without multilang plugin installed, return 'all'
		 *   because then developer will see the options and recognise the lang param,
		 *   then may think about to "turn off" the function or handle different languages
		 */
		public static function get_language_defaults( $enabled = true ) {

			if ( ! $enabled ) {
				return false;
			}
			$multilang = array();

			// Fallbacks
			$default                = mb_substr( get_locale(), 0, 2 );
			$multilang['default']   = $default;
			$multilang['current']   = $default;
			$multilang['languages'] = array( $default );

			// List Plugins in priority order

			$language_plugins_active = self::get_active_multilang_plugins();

			// Get only non-false values
			$language_plugins_active = array_filter( $language_plugins_active );

			// get the first element priority of language plugins
			$language_plugin_priority = ( is_array( $language_plugins_active ) ) ? array_shift( $language_plugins_active ) : array();

			if ( ! empty( $language_plugin_priority ) && is_string( $language_plugin_priority ) ) {

				switch ( $language_plugin_priority ) {

					case( 'wpml' ):

						global $sitepress;
						$multilang['default'] = $sitepress->get_default_language();
						$multilang['current'] = $sitepress->get_current_language();
						$active_languages     = $sitepress->get_active_languages();

						if ( is_array( $active_languages ) ) {
							$multilang['languages'] = array_keys( $active_languages );
						}

						break; // case( 'wpml' )

					case( 'polylang' ):

						// These checks of function_exists() and method_exists() added as deactivating polylang was giving fatal error

						global $polylang;

						if ( function_exists( 'pll_current_language' ) ) {
							$current = pll_current_language();
						}

						if ( function_exists( 'pll_default_language' ) ) {
							$default = pll_default_language();
						}

						if (
							// if i do not check for is_object( $polylang ), it gives $polylang as NULL
							// in short, these polylang methods not available to us while calling from plugin (not exopite framework)
							is_object( $polylang ) &&
							property_exists( $polylang, 'model' ) &&
							method_exists( $polylang->model, 'get_languages_list' )
						) {
							$poly_langs = $polylang->model->get_languages_list();
						}


						if ( isset( $poly_langs ) && is_array( $poly_langs ) ) {
							foreach ( $poly_langs as $p_lang ) {
								$languages[ $p_lang->slug ] = $p_lang->slug;
							}
						}


						$multilang['default'] = $default;
						// When all languages selected, then $current is false, so make $current as $default
						$multilang['current']   = ( isset( $current ) && $current ) ? $current : $multilang['current'];
						$multilang['languages'] = ( isset( $languages ) && $languages ) ? $languages : $multilang['languages'];

						break; // case( 'polylang' )

					case( 'qtran' ):
						global $q_config;
						$multilang['default']   = $q_config['default_language'];
						$multilang['current']   = $q_config['language'];
						$multilang['languages'] = qtranxf_getSortedLanguages( false );

						break;

					case( 'wpm' ):

						$multilang['default']   = wpm_get_default_language();
						$multilang['current']   = wpm_get_user_language();
						$multilang['languages'] = array_keys( wpm_get_languages() );

						break;

					default:

				}

			}


			$multilang = apply_filters( 'exopite_sof_language_defaults', $multilang );

			return ( ! empty( $multilang ) ) ? $multilang : false;

		}


		public static function get_current_language_code() {

			$multilang = self::get_language_defaults();

			return $multilang['current'];

		}

		public static function get_default_language_code() {

			$multilang = self::get_language_defaults();

			return $multilang['default'];

		}

	}

}
