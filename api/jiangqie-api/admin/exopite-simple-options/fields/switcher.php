<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Switcher
 *
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_switcher' ) ) {

	class Exopite_Simple_Options_Framework_Field_switcher extends Exopite_Simple_Options_Framework_Fields {

		public function __construct( $field, $value = '', $unique = '', $config = array() ) {
			parent::__construct( $field, $value, $unique, $config );
		}

		public function output() {

			echo $this->element_before();
			$label = ( isset( $this->field['label'] ) ) ? '<div class="exopite-sof-text-desc">' . $this->field['label'] . '</div>' : '';

			$classes = ( isset( $this->field['class'] ) ) ? implode( ' ', explode( ' ', $this->field['class'] ) ) : '';

			echo '<label class="checkbox">';
			echo '<input name="' . $this->element_name() . '" value="yes" class="checkbox__input ' . $classes . '" type="checkbox"' . $this->element_attributes() . checked( $this->element_value(), 'yes', false ) . '>';
			echo '<div class="checkbox__switch"></div>';
			echo '</label>' . $label;
			echo $this->element_after();

		}

	}

}
