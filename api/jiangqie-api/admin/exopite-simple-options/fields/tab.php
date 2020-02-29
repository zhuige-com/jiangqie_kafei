<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Tab
 *
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_tab' ) ) {

	class Exopite_Simple_Options_Framework_Field_tab extends Exopite_Simple_Options_Framework_Fields {

		public function __construct( $field, $value = '', $unique = '', $config = array(), $multilang ) {
			parent::__construct( $field, $value, $unique, $config, $multilang );
		}

		public function output() {

			echo $this->element_before();

			$unallows = array();
			$tabs    = array_values( $this->field['tabs'] );
			$unique_id = ( ! empty( $this->unique ) ) ? $this->unique : $this->field['id'];

			$self  = new Exopite_Simple_Options_Framework( array(
				'id' => $this->element_name(),
				'multilang' => $this->config['multilang'],
				'is_options_simple' => $this->config['is_options_simple'],
			), null );

			echo '<div class="exopite-sof-tabs">';

			$i = 0;

			$equal_width = ( isset( $this->field['options']['equal_width'] ) && $this->field['options']['equal_width'] ) ? ' equal-width' : '';

			/**
			 * Tab navigation
			 */
			echo '<ul class="exopite-sof-tab-header' . $equal_width . '">';

			foreach ( $tabs as $key => $tab ) {

				reset( $tabs );
				$tab_active = ( $key === key( $tabs ) ) ? ' active' : '';

				echo '<li class="exopite-sof-tab-link' . $tab_active . '">' . $tab['title'] . '</li>';

			}

			echo '</ul>';

			/**
			 * Tab content
			 */
			foreach ( $tabs as $key => $tab ) {

				reset( $tabs );
				$tab_active = ( $key === key( $tabs ) ) ? ' active' : '';

				echo '<div class="exopite-sof-tab-content' . $tab_active . '">';
				echo '<div class="exopite-sof-tab-mobile-header">' . $tab['title'] . '</div>';
				echo '<div class="exopite-sof-tab-content-body">';
				echo '<div class="exopite-sof-tab-content-body-inner">';

				foreach ( $tab['fields'] as $field ) {

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

				}

				echo '</div>';
				echo '</div>';
				echo '</div>';

			}

			echo '</div>';

			echo $this->element_after();

		}

	}

}
