<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Sanitize Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
/**
 * loop reqursive $valuePOST, get the field from fields based on "name" == "id"
 * - sanizite by type
 * - ignore if not exist
 * - in this case it is irrelevant how many times a filed exist -> ok for groups
 *
 * https://www.sitepoint.com/community/t/best-way-to-do-array-search-on-multi-dimensional-array/16382/3
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Sanitize' ) ) {

	class Exopite_Simple_Options_Framework_Sanitize {

		public $is_multilang;
		public $lang_current;
		public $config;
		public $fields;

		public static $parent_key;

		public function __construct( $is_multilang, $lang_current, $config, $fields ) {

			$this->is_multilang = $is_multilang;
			$this->lang_current = $lang_current;
			$this->config = $config;
			$this->fields = $fields;

		}

		public function get_sanitized_values( $fields, $posted_data ) {

			// $this->write_log( 'post', var_export( $posted_data, true ) );

			$posted_data = $this->array_search_and_replace( $posted_data );

			// $this->write_log( 'post-after', var_export( $posted_data, true ) );

			return $posted_data;

		}

		/**
		 * Array infos:
		 * https://stackoverflow.com/questions/7003559/use-strings-to-access-potentially-large-multidimensional-arrays
		 * https://stackoverflow.com/questions/6625808/how-can-i-use-a-php-array-as-a-path-to-target-a-value-in-another-array/6625931#6625931
		 */

		/**
		 * Loop $_POST recursive.
		 */
		public function array_search_and_replace( &$arr ) {

			foreach ( $arr as $key => $value ) {

				// If array, then call self.
				if( is_array( $value ) ) {

					if ( ! is_int( $key ) ) self::$parent_key = $key;
					$this->array_search_and_replace( $arr[$key] );

				} else {

					$field = null;

					/**
					 * Get field by id
					 */
					if ( ! is_int( $key ) ) {
						$field = $this->find_sub_array( 'id', $key, $this->fields );
						if ( $field == null ) {
							$field = $this->find_sub_array( 'id', self::$parent_key, $this->fields );
						}
					} else {
						$field = $this->find_sub_array( 'id', self::$parent_key, $this->fields );
					}

					/**
					 * Here we have all the necessarily information to sanitize.
					 * [key,] field and value
					 */

					$arr[$key] = $this->sanitize( $field, $value );

					// $this->write_log( 'test_print_ext', var_export( self::$parent_key, true ) . PHP_EOL . var_export( $field, true ) . PHP_EOL . var_export( $key, true ) . PHP_EOL . var_export( $value, true ) . PHP_EOL . '----' . PHP_EOL );

				}

			}

			return $arr;

		}

		/**
		 * Find field by id.
		 */
		public function find_sub_array( $needle_key, $needle_value, $array ) {

			if ( isset( $array[$needle_key] ) && $array[$needle_key] == $needle_value ) {
				return $array;
			}

			foreach( $array as $key => $value ) {

				if ( is_array( $value ) ) {

					if ( isset( $value[$needle_key] ) && $value[$needle_key] == $needle_value ) {
						return $value;
					} else {
						$finded = $this->find_sub_array( $needle_key, $needle_value, $value );

						if( $finded !== null ) {
							return $finded;
						}

					}

				}

			}

			return null;

		}

		/**
		 * Validate and sanitize values
		 *
		 * @param $field
		 * @param $value
		 *
		 * @return mixed
		 */
		public function sanitize( $field, $dirty_value ) {

			$dirty_value = isset( $dirty_value ) ? $dirty_value : '';

			// if $config array has sanitize function, then call it
			if (
				isset( $field['sanitize'] ) &&
				! empty( $field['sanitize'] ) &&
				(
					function_exists( $field['sanitize'] ) ||
					is_callable( $field['sanitize'] )
				)

			) {

				$sanitize_func_name = $field['sanitize'];

				$clean_value = call_user_func( $sanitize_func_name, $dirty_value );

			} else {

				// if $config array does not have sanitize function, do sanitize on field type basis
				$clean_value = $this->get_sanitized_field_value_by_type( $field, $dirty_value );

			}

			return apply_filters( 'exopite_sof_sanitize_value', $clean_value, $dirty_value, $field, $this->config );

		}

		/**
		 * Pass the field and value to run sanitization by type of field
		 *
		 * @param array $field
		 * @param mixed $value
		 *
		 * $return mixed $value after sanitization
		 */
		public function get_sanitized_field_value_by_type( $field, $value ) {

			$field_type = ( isset( $field['type'] ) ) ? $field['type'] : '';

			switch ( $field_type ) {

				case 'editor':
					// no break
				case 'textarea':
					/**
					 * HTML excepted accept <textarea>.
					 * @link https://codex.wordpress.org/Function_Reference/wp_kses_allowed_html
					 */
					$allowed_html = wp_kses_allowed_html( 'post' );
					// Remove '<textarea>' tag
					unset ( $allowed_html['textarea'] );
					/**
					 * wp_kses_allowed_html return the wrong values for wp_kses,
					 * need to change "true" -> "array()"
					 */
					array_walk_recursive(
						$allowed_html,
						function (&$value) {
							if (is_bool($value)) {
								$value = array();
							}
						}
					);
					// Run sanitization.
					$value = wp_kses( $value, $allowed_html );
					break;

				case 'ace_editor':
					/**
					 * TODO:
					 * This is basically also a textarea.
					 * Here user can save code, like JS or CSS or even PHP
					 * depense of the use of the field, this can be like
					 * "paste your google analytics code here".
					 *
					 * What we could do, is escape for SQL
					 *
					 * esc_textarea, wp_kses or wp_kses_post will remove this.
					 * textarea will escape all ´'´, ´"´ or ´<´ (<li></li> etc...)
					 * $value = esc_textarea( $value );
					 * $value = wp_kses_post( $value );
					 */
					$value = stripslashes( $value );
					break;

				case 'switcher':
					// no break
				case 'checkbox':
					/**
					 * In theory this will be never an array.
					 * Maybe in the future?
					 */
					if ( is_array( $value ) ) {
						foreach( $value as &$item ) {
							$item = ( $value === 'yes' ) ? 'yes' : 'no';
						}
					} else {
						$value = ( $value === 'yes' ) ? 'yes' : 'no';
					}
					break;

				case 'range':
					// no break
				case 'number':

					$value = ( is_numeric( $value ) ) ? $value : 0;
					if ( isset( $field['min'] ) && $value < $field['min'] ) {
						$value = $field['min'];
					}
					if ( isset( $field['max'] ) && $value > $field['max'] ) {
						$value = $field['max'];
					}

					break;

				default:
					$value = ( ! empty( $value ) ) ? sanitize_text_field( $value ) : '';

			}

			return $value;

		}

		//DEGUB
		public function write_log( $type, $log_line ) {

			$hash        = '';
			$fn          = plugin_dir_path( __FILE__ ) . '/' . $type . $hash . '.log';
			$log_in_file = file_put_contents( $fn, date( 'Y-m-d H:i:s' ) . ' - ' . $log_line . PHP_EOL, FILE_APPEND );

		}

	}

}
