<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Textarea
 *
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_textarea' ) ) {

	class Exopite_Simple_Options_Framework_Field_textarea extends Exopite_Simple_Options_Framework_Fields {

		public function __construct( $field, $value = '', $unique = '', $config = array() ) {
			parent::__construct( $field, $value, $unique, $config );
		}

		public function output() {

			echo $this->element_before();
			echo '<textarea name="' . $this->element_name() . '"' . $this->element_class() . $this->element_attributes() . '>' . $this->element_value() . '</textarea>';
			echo $this->element_after();

		}


	}

}
