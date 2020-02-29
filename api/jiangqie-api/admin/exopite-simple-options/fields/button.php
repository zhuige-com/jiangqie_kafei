<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Button
 *
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_button' ) ) {

	class Exopite_Simple_Options_Framework_Field_button extends Exopite_Simple_Options_Framework_Fields {

		public function __construct( $field, $value = '', $unique = '', $config = array() ) {

			parent::__construct( $field, $value, $unique, $config );

			$defaults = array(
				'href'      => '#',
				'target'    => '_self',
				'value'     => 'button',
				'btn-class' => 'exopite-sof-btn',
			);

			$options = ( ! empty( $this->field['options'] ) ) ? $this->field['options'] : array();

			$this->field['options'] = wp_parse_args( $options, $defaults );
		}

		public function output() {

			$classes = ( isset( $this->field['class'] ) ) ? implode( ' ', explode( ' ', $this->field['class'] ) ) : '';

			echo $this->element_before();
			echo '<a href="' . $this->field['options']['href'] . '" target="' . $this->field['options']['target'] . '"  class="' . $this->field['options']['btn-class'] . ' ' . $classes . '"' . $this->element_attributes() . '/>' . $this->field['options']['value'] . '</a>';
			echo $this->element_after();

		}

	}

}
