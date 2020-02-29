<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Fieldset
 *
 * - to group elements
 * - set element amount per row (1,2,3,4,6) <- like bootstrap
 * - title (if more then one) top or bottom of element
 *
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_fieldset' ) ) {

	class Exopite_Simple_Options_Framework_Field_fieldset extends Exopite_Simple_Options_Framework_Fields {

		public function __construct( $field, $value = '', $unique = '', $config = array(), $multilang ) {
			parent::__construct( $field, $value, $unique, $config, $multilang );
		}

		public function output() {

			echo $this->element_before();

			$unallows = array();
			$unique_id = ( ! empty( $this->unique ) ) ? $this->unique : $this->field['id'];

			$self  = new Exopite_Simple_Options_Framework( array(
				'id' => $this->element_name(),
				'multilang' => $this->config['multilang'],
				'is_options_simple' => $this->config['is_options_simple'],
			), null );

			$i = 0;

			$fields = $this->field['fields'];

			echo '<div class="container">';
			echo '<div class="row">';

			/**
			 * 1 -> col-12 col-lg-12 (12/index)
			 * 2 -> col-12 col-lg-6
			 * 3 -> col-12 col-lg-4
			 * 4 -> col-12 col-lg-3
			 * 6 -> col-12 col-lg-2
			 */

			$col_classes = array( 'col', 'col-xs-12' );
			$allowed_cols = array( 1, 2, 3, 4, 6 );
			$col_number = ( isset( $this->field['options']['cols'] ) ) ? intval( $this->field['options']['cols'] ) : 1;
			if ( ! in_array( $col_number, $allowed_cols ) ) {
				$col_number = 1;
			} else {
				$col_classes[] = 'col-lg-' . ( 12 / $col_number );
				$col_classes[] = 'exopite-sof-col-lg';
			}

			foreach ( $fields as $field ) {

				echo '<div class="' . implode( ' ', $col_classes ) . '">';

				if ( in_array( $field['type'], $unallows ) ) {
					$field['_notice'] = true;
					continue;
				}

				if ( is_serialized( $this->value ) ) {
					$this->value = unserialize( $this->value );
				}

				$field_value = '';
				if ( isset( $this->value[ $field['id'] ] ) ) {
					$field_value = $this->value[ $field['id'] ];
				} elseif ( isset( $field['default'] ) ) {
					$field_value = $field['default'];
				}

				$class = 'Exopite_Simple_Options_Framework_Field_' . $field['type'];

				echo $self->add_field( $field, $field_value );

				echo '</div>'; // col

			}

			echo '</div>'; // row
			echo '</div>'; // container

			echo $this->element_after();

		}

	}

}
