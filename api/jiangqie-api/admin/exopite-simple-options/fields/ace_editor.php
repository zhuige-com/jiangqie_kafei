<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Ace Editor
 *
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_ace_editor' ) ) {
	class Exopite_Simple_Options_Framework_Field_ace_editor extends Exopite_Simple_Options_Framework_Fields {

		public function __construct( $field, $value = '', $unique = '', $config = array() ) {

			parent::__construct( $field, $value, $unique, $config );

		}

		public function output() {

			$editor_id = $this->field['id'];

			$defaults = array(
				'theme'                     => 'ace/theme/chrome',
				'mode'                      => 'ace/mode/javascript',
				'showGutter'                => true,
				'showPrintMargin'           => true,
				'enableBasicAutocompletion' => true,
				'enableSnippets'            => true,
				'enableLiveAutocompletion'  => true,
			);

			$options = ( ! empty( $this->field['options'] ) ) ? $this->field['options'] : array();
			$options = json_encode( wp_parse_args( $options, $defaults ) );

			echo $this->element_before();

			echo '<div class="exopite-sof-ace-editor-wrapper">';
			echo '<div id="exopite-sof-ace-' . $editor_id . '" class="exopite-sof-ace-editor"' . $this->element_attributes() . '></div>';
			echo '</div>';

			echo '<textarea class="exopite-sof-ace-editor-textarea hidden" name="' . $this->element_name() . '">' . $this->element_value() . '</textarea>';
			echo '<textarea class="exopite-sof-ace-editor-options hidden">' . $options . '</textarea>';

			echo $this->element_after();

		}

		public static function enqueue( $args ) {

			//https://cdnjs.com/libraries/ace/

//			wp_enqueue_script( 'ace-editor', '//cdnjs.cloudflare.com/ajax/libs/ace/1.2.4/ace.js', array( 'jquery' ), '1.2.4', true );
//
//			wp_enqueue_script( 'ace-editor-language_tool', '//cdnjs.cloudflare.com/ajax/libs/ace/1.2.9/ext-language_tools.js', array( 'ace-editor' ), '1.2.4', true );
//
//			$script_file = 'ace-loader.min.js';
//			$script_name = 'exopite-sof-ace-loader';
//
//			wp_enqueue_script( $script_name, $args['plugin_sof_url'] . 'assets/' . $script_file, array( 'ace-editor-language_tool' ), filemtime( join( DIRECTORY_SEPARATOR, array(
//				$args['plugin_sof_path'] . 'assets',
//				$script_file
//			) ) ), true );

			/**
			 * For some reason this is not working. :-O
			 * Maybe ace.js file is corrupted? Tried to download twice.
			 */
			$resources = array(
				array(
					'name'       => 'ace-editor',
					'fn'         => 'editors/ace/ace.js',
					'type'       => 'script',
					'dependency' => array( 'jquery' ),
					'version'    => '1.2.4',
					'attr'       => true,
				),
				array(
					'name'       => 'ace-editor-language_tool',
					'fn'         => 'editors/ace/ext-language_tools.js',
					'type'       => 'script',
					'dependency' => array( 'ace-editor' ),
					'version'    => '1.2.4',
					'attr'       => true,
				),
				array(
					'name'       => 'exopite-sof-ace-loader',
					'fn'         => 'ace-loader.min.js',
					'type'       => 'script',
					'dependency' => array( 'ace-editor-language_tool' ),
					'version'    => '',
					'attr'       => true,
				),

			);

			parent::do_enqueue( $resources, $args );

		}

	}
}
