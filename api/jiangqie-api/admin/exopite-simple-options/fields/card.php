<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Card
 *
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_card' ) ) {

	class Exopite_Simple_Options_Framework_Field_card extends Exopite_Simple_Options_Framework_Fields {

		public function __construct( $field, $value = '', $unique = '', $config = array() ) {
			parent::__construct( $field, $value, $unique, $config );
		}

		public function output() {

			$classes = ( isset( $this->field['class'] ) ) ? implode( ' ', explode( ' ', $this->field['class'] ) ) : '';

			echo $this->element_before();

			echo '<div class="card ' . $classes . '">';

			if ( ! empty( $this->field['header'] ) ) {
				echo '<div class="card-header">';
				echo $this->field['header'];
				echo '</div>';
			}

			echo '<div class="card-body">';

			if ( ! empty( $this->field['title'] ) ) {
				echo '<h4 class="card-title">' . $this->field['title'] . '</h4>';
			}

			echo '<div class="card-text">' . $this->field['content'] . '</div>';

			echo '</div>';

			if ( ! empty( $this->field['footer'] ) ) {
				echo '<div class="card-footer text-muted">';
				echo $this->field['footer'];
				echo '</div>';
			}

			echo '</div>';

			echo $this->element_after();

		}

	}

}
