<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Typography
 *
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_typography' ) ) {

	class Exopite_Simple_Options_Framework_Field_typography extends Exopite_Simple_Options_Framework_Fields {

		public function __construct( $field, $value = '', $unique = '', $config = array(), $multilang ) {
			parent::__construct( $field, $value, $unique, $config, $multilang );

		}

		public function output() {

			echo $this->element_before();

			$defaults_value = array(
				'family'       => 'Arial',
				'variant'      => '400',
				'font'         => 'websafe',
				'size'         => '14',
				'height'       => '',
				'color'        => '',
			);

			$default_variants = apply_filters( 'exopite_sof_websafe_fonts_variants', array(
				'regular',
				'italic',
				'700',
				'700italic',
				'inherit'
			));

			$websafe_fonts = apply_filters( 'exopite_sof_websafe_fonts', array(
				'Arial',
				'Arial Black',
				'Comic Sans MS',
				'Impact',
				'Lucida Sans Unicode',
				'Tahoma',
				'Trebuchet MS',
				'Verdana',
				'Courier New',
				'Lucida Console',
				'Georgia, serif',
				'Palatino Linotype',
				'Times New Roman'
			));

			$value         = wp_parse_args( $this->element_value(), $defaults_value );
			$family_value  = $value['family'];
			$variant_value = $value['variant'];
			$is_variant    = ( isset( $this->field['variant'] ) && $this->field['variant'] === false ) ? false : true;
			$is_chosen     = ( isset( $this->field['chosen'] ) && $this->field['chosen'] === false ) ? '' : 'chosen ';
			$google_json   = $this->get_google_fonts_json();
			$chosen_rtl    = ( is_rtl() && ! empty( $is_chosen ) ) ? 'chosen-rtl ' : '';

			//Container
			echo '<div class="exopite-sof-font-field exopite-sof-font-field-js" data-id="'.$this->field['id'].'">';

			if( is_object( $google_json ) ) {

				$googlefonts = array();

				foreach ( $google_json->items as $key => $font ) {
					$googlefonts[$font->family] = $font->variants;
				}

				$is_google = ( array_key_exists( $family_value, $googlefonts ) ) ? true : false;

				echo '<label class="exopite-sof-typography-family">';
				echo '<select name="'. $this->element_name( '[family]' ) .'" class="'. $is_chosen . $chosen_rtl .'exopite-sof-typo-family" data-atts="family"'. $this->element_attributes() .'>';

				do_action( 'exopite_sof_typography_family', $family_value, $this );

				echo '<optgroup label="'. esc_attr__( 'Web Safe Fonts', 'exopite-sof' ) .'">';
				foreach ( $websafe_fonts as $websafe_value ) {
					echo '<option value="'. $websafe_value .'" data-variants="'. implode( '|', $default_variants ) .'" data-type="websafe"'. selected( $websafe_value, $family_value, true ) .'>'. $websafe_value .'</option>';
				}
				echo '</optgroup>';

				echo '<optgroup label="'. esc_attr__( 'Google Fonts', 'exopite-sof' ) .'">';
				foreach ( $googlefonts as $google_key => $google_value ) {
					echo '<option value="'. $google_key .'" data-variants="'. implode( '|', $google_value ) .'" data-type="google"'. selected( $google_key, $family_value, true ) .'>'. $google_key .'</option>';
				}
				echo '</optgroup>';

				echo '</select>';
				echo '</label>';

				if( ! empty( $is_variant ) ) {

					$variants = ( $is_google ) ? $googlefonts[$family_value] : $default_variants;
					$variants = ( $value['font'] === 'google' || $value['font'] === 'websafe' ) ? $variants : array( 'regular' );

					echo '<label class="exopite-sof-typography-variant">';
					echo '<select name="'. $this->element_name( '[variant]' ) .'" class="'. $is_chosen . $chosen_rtl .'exopite-sof-typo-variant" data-atts="variant">';
					foreach ( $variants as $variant ) {
						echo '<option value="'. $variant .'"'. $this->checked( $variant_value, $variant, 'selected' ) .'>'. $variant .'</option>';
					}
					echo '</select>';
					echo '</label>';

				}

				$self  = new Exopite_Simple_Options_Framework( array(
					'id' => $this->element_name(),
					'multilang' => $this->config['multilang'],
					'is_options_simple' => $this->config['is_options_simple'],
				), null );

				$self->include_field_class( array( 'type' => 'number' ) );
				$self->include_field_class( array( 'type' => 'color' ) );
				$self->enqueue_field_class( array( 'type' => 'color' ) );

				$field_size = array(
					'id'      => 'size',
                    'type'    => 'number',
                    'default' =>  ( isset( $this->field['default']['size'] ) ) ? $this->field['default']['size'] : '',
					// 'before'  => 'Size ',
					'pseudo'  => true,
					'class' => 'font-size-js',
					'prepend' => 'fa-font',
					'append' => 'px',
                );

				$field_height = array(
					'id'      => 'height',
                    'type'    => 'number',
                    'default' =>  ( isset( $this->field['default']['height'] ) ) ? $this->field['default']['height'] : '',
					// 'before'  => 'Height ',
					'prepend' => 'fa-arrows-v',
					'append' => 'px',
					'pseudo'  => true,
					'class' => 'line-height-js',
                );

				$field_color = array(
					'id'      => 'color',
                    'type'    => 'color',
                    'rgba'   => true,
                    'default' =>  ( isset( $this->field['default']['color'] ) ) ? $this->field['default']['color'] : '',
					// 'before'  => 'Color ',
					'pseudo'  => true,
					'class' => 'font-color-js',
                );

				echo '<div class="exopite-sof-typography-size-height">';
				echo $self->add_field( $field_size, $value['size'] );
				echo $self->add_field( $field_height, $value['height'] );
				echo '</div>';
				echo '<div class="exopite-sof-typography-color">';
				echo $self->add_field( $field_color, $value['color'] );
				echo '</div>';

				/**
				 * Font Preview
				 */
				if (isset( $this->field['preview'] ) && $this->field['preview'] == true) {
					if (isset( $this->field['preview_text'] )) {
						$preview_text = $this->field['preview_text'];
					}else {
						$preview_text = 'Lorem ipsum dolor sit amet, pro ad sanctus admodum, vim at insolens appellantur. Eum veri adipiscing an, probo nonumy an vis.';
					}
					echo '<div class="exopite-sof-font-preview">'. $preview_text .'</div>';
				}

				echo '<input type="text" name="'. $this->element_name( '[font]' ) .'" class="exopite-sof-typo-font hidden" data-atts="font" value="'. $value['font'] .'" />';

			} else {

				echo esc_attr__( 'Error! Can not load json file.', 'exopite-sof' );

			}

			//end container
			echo '</div>';

			echo $this->element_after();

		}

		public static function enqueue( $args ) {

			/**
			 * https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.jquery.min.js
			 * https://www.sitepoint.com/jquery-select-box-components-chosen-vs-select2/
			 */
			$resources = array(
				array(
					'name'       => 'jquery-chosen',
					'fn'         => 'chosen.min.css',
					'type'       => 'style',
					'dependency' => array(),
					'version'    => '1.8.2',
					'attr'       => 'all',
				),
				array(
					'name'       => 'jquery-chosen',
					'fn'         => 'chosen.jquery.min.js',
					'type'       => 'script',
					'dependency' => array( 'jquery' ),
					'version'    => '1.8.2',
					'attr'       => true,
				),
				array(
					'name'       => 'exopite-sof-jquery-chosen-loader',
					'fn'         => 'loader-jquery-chosen.min.js',
					'type'       => 'script',
					'dependency' => array( 'jquery-chosen' ),
					'version'    => '20190407',
					'attr'       => true,
				),
			);

			parent::do_enqueue( $resources, $args );

		}

	}

}
