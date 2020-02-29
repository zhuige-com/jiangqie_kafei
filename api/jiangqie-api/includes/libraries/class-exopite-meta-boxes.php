<?php

/**
 * Version: 20190716
 *
 * ToDos:
 * - add error messages
 */

if ( ! class_exists( 'Exopite_Meta_Boxes' ) ) :

class Exopite_Meta_Boxes {

    /**
     * HOW TO USE
     *
     * Fields:
     * - content
     * - hidden
     * - submit
     * - number
     * - select
     * - checkbox (single/multiple)
     * - radio
     * - text
     * - password
     * - upload (image)
     * - gallery
     * - tab
     *
     * Specific to fields:
     *     select:
     *         - options
     *     checkbox, radio:
     *         - config
     *         - options
     *     for all:
     *         - attributes
     *         - unit
     *         - before
     *         - after
     *         - default
     *
     *   // Enqueue meta box style and script with the plugin
	 *	 // Add metabox to custom post type
	 *	$metabox_args = array(
     *	    'cpt_name' => array(
     *	        'id' => 'meta_box_unique',
     *	        'title' => 'title',
     *          'post-context' => 'advanced' | 'normal' | 'side',
     *          'post-priority' => 'high' | 'low',
     *	        'wrapper-class' => 'wrapper',
     *	        'wrapper-selector' => 'div',
     *	        'row-class' => 'row',
	 *			'row-selector' => 'div',
	 *			// 'debug' => true,
     *	        'fields' => array(
     *	            'content_unique' => array(
     *	                'title' => 'Something to say',
	 *					'type' => 'content',
	 *					'default' => 'This is the text of the content field.',
     *	            ),
     *	            'text_unique' => array(
     *	                'title' => 'Price',
	 *					'type' => 'text',
	 *					'unit' => '€',
	 *					'attributes' => array(
	 *						'style' => 'max-width:375px;',
	 *					),
     *	            ),
     *	            'hidden_unique' => array(
     *	                'title' => 'Hidden',
	 *					'type' => 'hidden',
	 *					'default' => 'hidden_text'
     *	            ),
     *	            'submit_unique' => array(
     *	                'title' => 'Submit',
	 *					'type' => 'submit',
	 *					'default' => 'Submit button',
     *	            ),
     *	            'number_unique' => array(
     *	                'title' => 'Number',
	 *					'type' => 'number',
	 *					'default' => 5,
	 *					'attributes' => array(
	 *						'min' => '3',
	 *						'max' => '8',
	 *					),
     *	            ),
     *	            'text_with_attrs' => array(
     *	                // 'title' => 'Title is optional',
	 *					'type' => 'text',
	 *					'attributes' => array(
	 *						'placeholder' => 'Placeholder',
	 *						'disabled' => 'disabled',
	 *					),
     *
     *	            ),
     *	            'select_unique' => array(
     *	                'title' => 'Select',
	 *					'type' => 'select',
	 *					'options' => $this->some_function(),
	 *				),
	 *				'checkbox_unique' => array(
	 *					'title' => 'Checkbox',
	 *					'type' => 'checkbox',
	 *				),
	 *				'checkbox_multiple_unique' => array(
	 *					'title' => 'Multiple Checkboxes',
	 *					'type' => 'checkbox',
	 *					'options' => array(
	 *					    'option-1' => 'Option 1',
	 *						'option-2' => 'Option 2',
	 *						'option-3' => 'Option 3',
	 *					),
	 *					// 'config' => array(
	 *					// 	'style' => 'vertical',
	 *					// ),
	 *				),
	 *				'radio_unique' => array(
	 *					'title' => 'Radio',
	 *					'type' => 'radio',
	 *					'options' => array(
	 *					    'option-1' => 'Option 1',
	 *						'option-2' => 'Option 2',
	 *						'option-3' => 'Option 3',
	 *					),
	 *					'config' => array(
	 *						'style' => 'vertical',
	 *					),
	 *				),
	 *				'radio_default_unique' => array(
	 *					'title' => 'Radio',
	 *					'type' => 'radio',
	 *					'options' => array(
	 *					    'option-1' => 'Option 1',
	 *						'option-2' => 'Option 2',
	 *						'option-3' => 'Option 3',
	 *					),
	 *					'default' => 'option-3',
     *				),
     *  			'tabs_unique' => array(
	 * 					'type' => 'tab',
	 * 					'first_tab_unique' => array(
	 * 						'type' => 'section',
	 * 						'title' => 'First Tab',
	 * 						'fields' => array(
	 * 							'tab_text_1_unique' => array(
	 * 								'title' => 'Text 1',
	 * 								'type' => 'text',
	 * 								'attributes' => array(
	 * 									'style' => 'max-width:375px;',
	 * 								),
	 * 							),
	 * 							'tab_text_2_unique' => array(
	 * 								'title' => 'Text 2',
	 * 								'type' => 'text',
	 * 								'attributes' => array(
	 * 									'style' => 'max-width:375px;',
	 * 								),
	 * 							),
	 * 						),
	 * 					),
	 * 					'second_tab_unique' => array(
	 * 						'type' => 'section',
	 * 						'title' => 'Second Tab',
	 * 						'fields' => array(
	 * 							'tab_text_3_unique' => array(
	 * 								'title' => 'Text 3',
	 * 								'type' => 'text',
	 * 								'attributes' => array(
	 * 									'style' => 'max-width:375px;',
	 * 								),
	 * 							),
	 * 							'tab_text_4_unique' => array(
	 * 								'title' => 'Text 4',
	 * 								'type' => 'text',
	 * 								'attributes' => array(
	 * 									'style' => 'max-width:375px;',
	 * 								),
	 * 							),
	 * 						),
	 * 					),
     *
	 * 				),
     *              'gallery_unique' => array(
     *	                'title' => 'Gallery',
	 *					'type' => 'gallery',
	 *					'options' => array(
	 *						'add_button' => 'Add images',
	 *						'media_frame_title' => 'Select images for gallery',
	 *						'media_frame_button' => 'Add',
	 *						'media_type' => 'image',
	 *					),
	 *				),
     *	        ),
     *
     *	   	),
	 *	);
     *
     *	$plugin_meta_boxes = new Exopite_Meta_Boxes( $metabox_args, PLUGIN_URL );
     *
     * - OR -
     *
     *	$plugin_meta_boxes = new Exopite_Meta_Boxes( $metabox_args, $this->jiangqie_api );
     *
     * For WordPress Plugin Boilderplate
     * includes/class-jiangqie-api.php
     * private function load_dependencies() {
     *     // ...
     *     require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/libraries/class-exopite-meta-boxes.php';
     *     // ...
     * }
     *
     * private function define_admin_hooks() {
     *     // ...
     *     $this->loader->add_action( 'init', $plugin_admin, 'create_metaboxex', 999 );
     *     // ...
     * }
     *
     * admin/class-om-links-admin.php
     *
     * public function enqueue_styles() {
     *     // ...
     *     wp_enqueue_style( 'exopite-meta-box-style', plugin_dir_url( __FILE__ ) . 'css/exopite-meta-box-style.css', '20190716', 'all' );
     *     // ...
     * }
     */
    protected $args = array();

    protected $plugin_url;

    public function __construct( $args, $plugin_url ) {

        $this->args = $args;
        $this->plugin_url = $plugin_url;

        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

        /**
         * Add metabox and register custom fields
         *
         * @link https://code.tutsplus.com/articles/rock-solid-wordpress-30-themes-using-custom-post-types--net-12093
         */
        add_action( 'admin_init', array( $this, 'render_meta_options' ) );
        add_action( 'save_post', array( $this, 'save_meta_options' ) );

    }

    public function admin_enqueue_scripts() {

        // wp_enqueue_script( 'jquery-ui-core');

        /**
         * Enqueue style and script with the plugin
         */

        // wp_enqueue_style( 'exopite_meta_box_style_css', join( '/', array( rtrim( $this->plugin_url, '/' ), 'admin', 'css', 'exopite-meta-box-style.css' ) ) );
        // wp_enqueue_script( 'exopite-meta-box-script_js', join( '/', array( rtrim( $this->plugin_url, '/' ), 'admin', 'js', 'exopite-meta-box-script.js' ) ), array( 'jquery' ), '1.0.0', true );

    }

    /* Create a meta box for our custom fields */
    public function render_meta_options() {

        foreach ( $this->args as $cpt_name => $options ) {

            /**
             *  add_meta_box(
             *      string $id,
             *      string $title,
             *      callable $callback,
             *      string|array|WP_Screen $screen = null,
             *      string $context = 'advanced',
             *      string $priority = 'default',
             *      array $callback_args = null
             *  )
             *
             * @link https://developer.wordpress.org/reference/functions/add_meta_box/
             */
            add_meta_box(
                $options["id"],
                $options["title"],
                array( $this, "render_meta_box" ),
                $cpt_name,
                $options["post-context"],
                $options["post-priority"]
            );

        }

    }

    public function get_wrapper_start( $options ) {

        ?>
        <<?php echo $options['wrapper-selector']; ?> class="exopite-meta-boxes-wrapper <?php echo $options['wrapper-class']; ?>">
        <?php

    }

    public function get_wrapper_end( $options ) {

        ?>
        </<?php echo $options['wrapper-selector']; ?>>
        <?php

    }

    public function get_row_start( $name, $options, $field ) {

        $row_classes = 'meta-row';
        if ( isset( $options['row-class'] ) ) {
            $row_classes .= ' ' . $options['row-class'];
        }

        $field_title = ( isset( $field['title'] ) ) ? $field['title'] : '';
        $label_class = '';
        if ( empty( $field_title ) ) {
            $label_class = ' class="hidden"';
        }

        ?>
        <<?php echo $options['row-selector']; ?> class="<?php echo $row_classes; ?>"><label for="<?php echo $name; ?>"<?php echo $label_class; ?>><?php echo $field['title']; ?></label><span>
        <?php

    }

    public function get_row_end( $options ) {

        ?>
        </span></<?php echo $options['row-selector']; ?>>
        <?php

    }

    public function get_field_attributes( $name, $field ) {

        $id = 'id="' . $name .'"';

        $field_classes = '';
        if ( isset( $field['classes'] ) ) {
            $field_classes = ' class="' . $field['classes'] . '" ';
        }

        $attributes = array();
        if ( isset( $field['attributes'] ) ) {
            foreach ( $field['attributes'] as $key => $value ) {
                $attributes[] = $key . '="' . $value . '"';
            }
        }

        if ( $field['type'] == 'checkbox' && isset( $field['options'] ) ) {
            $name = $name . '[]';
        }

        /**
         * Remove id from multiple fields
         */
        if ( ( $field['type'] == 'checkbox' || $field['type'] == 'radio' ) && isset( $field['options'] ) ) {
            $id = '';
        }

        echo $id .  $field_classes . ' name="' . $name . '" ' . implode( ' ', $attributes );

    }

    public function get_field_before( $field ) {

        if ( isset( $field['before'] ) ) {

            echo $field['before'];

        }

    }

    public function get_field_after( $field ) {

        if ( isset( $field['after'] ) ) {

            echo $field['after'];

        }

    }

    public function get_field_unit( $field ) {

        if ( isset( $field['unit'] ) ) {

            $unit_classes = 'exopite-meta-boxes-unit muted';

            switch ( $field['type'] ) {
                case 'text':
                case 'textarea':
                    // $unit_classes .= ' exopite-meta-boxes-unit-fixed';
                    break;

            }
            echo '<span class="' . $unit_classes . '">' . $field['unit'] . '</span>';

        }

    }

    public function get_field_value( $name, $custom, $field ) {

        /**
         * ToDos:
         * - deal with defaults
         */

        $value = maybe_unserialize( $custom[$name][0] );

        if ( ! isset( $value ) ) {

            if ( isset( $field['default'] ) ) {
                $value = $field['default'];
            } else {
                $value = null;
            }

        }

        switch ( $field['type'] ) {

            case 'checkbox':

                if ( is_array( $value ) ) {

                    return $value;

                } else {

                    return esc_attr( $value );

                }
                break;

            case 'content':
                return $value;
                break;

            case 'textarea':
                return esc_textarea( $value );
                break;

            default:
                return esc_attr( $value );
                break;

        }

    }

    public function checked( $value, $key ) {

        if ( isset( $value ) && is_array( $value ) && in_array( $key, $value ) ) {
            echo " checked";
        }

    }

    public function add_regular_field( $name, $custom, $field ) {

        ?>
        <input type="<?php echo $field['type']; ?>" <?php $this->get_field_attributes( $name, $field ); ?> value="<?php echo $this->get_field_value( $name, $custom, $field ); ?>">
        <?php

    }

    public function add_content_field( $name, $custom, $field ) {

        echo $this->get_field_value( $name, $custom, $field );

    }

    public function add_password_field( $name, $custom, $field ) {

        ?>
        <input type="password" <?php $this->get_field_attributes( $name, $field ); ?> value="<?php echo $this->get_field_value( $name, $custom, $field ); ?>">
        <?php

    }

    public function add_textarea_field( $name, $custom, $field ) {

        ?>
        <textarea <?php $this->get_field_attributes( $name, $field ); ?>><?php echo $this->get_field_value( $name, $custom, $field ); ?></textarea>
        <?php

    }

    public function add_radio_field( $name, $custom, $field ) {

        if ( isset( $field['options'] ) ) {

            foreach ( $field['options'] as $key => $element ) :

                $label_attr = '';

                if ( isset( $field['config'] ) && isset( $field['config']['style'] ) && $field['config']['style'] == 'vertical' ) {
                    $label_attr = ' style="display:block;"';
                }

                ?>
                <label<?php echo $label_attr; ?>>
                    <input type="radio" <?php $this->get_field_attributes( $name, $field ); ?> value="<?php echo $key; ?>" <?php checked( $this->get_field_value( $name, $custom, $field ), $key ); ?> />
                    <?php
                        echo $element;
                    ?>
                </label>
                <?php

            endforeach;

        }

    }

    public function add_checkbox_field( $name, $custom, $field ) {

        if ( isset( $field['options'] ) ) {

            foreach ( $field['options'] as $key => $element ) :

                $label_attr = '';

                if ( isset( $field['config'] ) && isset( $field['config']['style'] ) && $field['config']['style'] == 'vertical' ) {
                    $label_attr = ' style="display:block;"';
                }

                ?>
                <label<?php echo $label_attr; ?>>
                    <input type="checkbox" <?php $this->get_field_attributes( $name, $field ); ?> value="<?php echo $key; ?>" <?php $this->checked( $this->get_field_value( $name, $custom, $field ), $key ); ?> />
                    <?php
                        echo $element;
                    ?>
                </label>
                <?php

            endforeach;

        } else {
            ?>
            <input type="checkbox" <?php $this->get_field_attributes( $name, $field ); ?> value="yes" <?php checked( $this->get_field_value( $name, $custom, $field ), 'yes' ); ?> />
            <?php
        }

    }

    public function add_select_field( $name, $custom, $field ) {

        ?>
        <select <?php $this->get_field_attributes( $name, $field ); ?>>
            <?php
            foreach ( $field['options'] as $key => $value ) :
                ?>
                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $this->get_field_value( $name, $custom, $field ) ); ?>><?php echo $value; ?></option>
                <?php
            endforeach;
            ?>
        </select>
        <?php

    }

    public function add_gallery_field( $name, $custom, $field ) {

        $defaults = array(
            'add_button' => esc_attr__( 'Add to gallery', 'exopite-meta-boxes' ),
            'media_frame_title' => esc_attr__( 'Select images for gallery', 'exopite-meta-boxes' ),
            'media_frame_button' => esc_attr__( 'Add', 'exopite-meta-boxes' ),
            'media_type' => 'image',
        );

        $options = ( isset( $field['options'] ) && is_array( $field['options'] ) ) ? $field['options'] : array();
        $options = wp_parse_args( $options, $defaults );

        $value = $this->get_field_value( $name, $custom, $field );
        ?>
        <div class="exopite-meta-boxes-gallery-field" data-media-frame-title="<?php echo esc_attr( $options['media_frame_title'] ); ?>" data-media-frame-button="<?php echo esc_attr( $options['media_frame_button'] ); ?>" data-media-frame-type="<?php echo esc_attr( $options['media_type'] ); ?>">
            <input type="hidden" <?php $this->get_field_attributes( $name, $field ); ?> data-control="gallery-ids" value="<?php echo $value ?>" />
            <div class="exopite-meta-boxes-gallery">
            <?php

            if ( $value ) :

                $meta_array = explode( ',', $value );
                foreach ( $meta_array as $meta_gall_item ) :

                    $src = wp_get_attachment_thumb_url( $meta_gall_item );
                    if ( ! $src ) {
                        $src = wp_get_attachment_url( $meta_gall_item );
                    }

                    if ( $this->is_video( $src ) ) {

                        ?><span class="exopite-meta-boxes-image-item"><span class="exopite-meta-boxes-image-delete"></span><video id="<?php echo esc_attr( $meta_gall_item ); ?>" src="<?php echo $src; ?>"></span><?php

                    } else {

                        ?><span class="exopite-meta-boxes-image-item"><span class="exopite-meta-boxes-image-delete"></span><img id="<?php echo esc_attr( $meta_gall_item ); ?>" src="<?php echo $src; ?>"></span><?php

                    }


                endforeach;

            endif;

            ?>
            </div>
            <input class="exopite-meta-boxes-gallery-add" type="button" value="<?php echo esc_attr( $options['add_button'] ); ?>" />
        </div>
        <?php

    }

    public function add_upload_field( $name, $custom, $field ) {

        $field['classes'] .= ' exopite-meta-boxes-upload-url';

        $value = $this->get_field_value( $name, $custom, $field );

        if ( isset( $field['config']['preview'] ) && $field['config']['preview'] ) :

            $preview_attrs = '';
            if ( isset( $value ) && ! empty( trim( $value ) ) && $this->is_image( $value ) ) {
                $preview_attrs = ' style="display:block;background-image:url(' . $value . ')"';
            }

            ?>
            <span class="exopite-meta-boxes-upload-preview"<?php echo $preview_attrs; ?>><span class="exopite-meta-boxes-upload-preview-close">×</span></span>
            <?php

        endif;

        /**
         * The actual field that will hold the URL for our file
         */
        ?>
        <input type="url" <?php $this->get_field_attributes( $name, $field ); ?> value="<?php echo $value; ?>">
        <?php
            /**
             * The button that opens our media uploader
             * The `data-media-uploader-target` value should match the ID/unique selector of your field.
             * We'll use this value to dynamically inject the file URL of our uploaded media asset into your field once successful (in the myplugin-media.js file)
             */
        ?>
        <button type="button" class="button exopite-meta-boxes-upload-button"><?php echo ( isset( $field['config']['button_text'] ) ) ? $field['config']['button_text'] : 'Upload'; ?></button>

        <?php

    }

    public function get_fields( $name, $custom, $options, $field ) {

        $this->get_row_start( $name, $options, $field );

        $this->get_field_before( $field );

        switch ( $field['type'] ) {

            case 'content':
                $this->add_content_field( $name, $custom, $field );
                break;

            case 'password':
                $this->add_password_field( $name, $custom, $field );
                break;

            case 'textarea':
                $this->add_textarea_field( $name, $custom, $field );
                break;

            case 'radio':
                $this->add_radio_field( $name, $custom, $field );
                break;

            case 'checkbox':
                $this->add_checkbox_field( $name, $custom, $field );
                break;

            case 'select':
                $this->add_select_field( $name, $custom, $field );
                break;

            case 'gallery':
                $this->add_gallery_field( $name, $custom, $field );
                break;

            case 'upload':
                $this->add_upload_field( $name, $custom, $field );
                break;

            case 'tab':

                echo '<div class="tabs">';

                $i = 0;

                foreach ( $field as $key => $value ) {

                    if ( is_array( $value ) && isset( $value['type'] ) && $value['type'] == 'section' && isset( $value['fields'] ) ) {

                        $section_title = ( isset( $value['title'] ) ) ? $value['title'] : 'Tab';

                        /**
                         * Maybe later allow select other tabs to be open on start. Maybe with URL param?
                         */
                        $checked = '';
                        if ( $i == 0 ) {
                            $checked = ' checked="checked" ';
                        }

                        echo '<input name="tabs" type="radio" id="tab-' . $i . '"' . $checked . 'class="input"/>';
                        echo '<label for="tab-' . $i . '" class="nav-tab">' . $section_title . '</label>';
                        echo '<div class="panel">';

                        foreach ( $value['fields'] as $tab_field_name => $tab_field ) {

                            $this->get_fields( $tab_field_name, $custom, $options, $tab_field );

                        }

                        echo '</div>';

                        $i++;

                    }

                }

                echo '</div>';

                break;

            default:
                $this->add_regular_field( $name, $custom, $field );
                break;

        }

        $this->get_field_unit( $field );

        $this->get_field_after( $field );

        $this->get_row_end( $options );

    }

    // Display meta box and custom fields
    public function render_meta_box() {

        // Get Post object
        global $post;

        // Get post custom
        $custom = get_post_custom( $post->ID );

        $post_type = get_post_type();

        // Generate nonce
        wp_nonce_field( 'meta_box_nonce', 'meta_box_nonce' );

        foreach ( $this->args as $cpt_name => $options ) {

            if ( isset( $options['debug'] ) && $options['debug'] ) {
                echo '<pre>';
                var_export( $custom );
                echo '</pre>';
            }

            if ( $post_type == $cpt_name ) {

                $this->get_wrapper_start( $options );

                foreach ( $options['fields'] as $name => $field ) {

                    $this->get_fields( $name, $custom, $options, $field );

                }

                $this->get_wrapper_end( $options );

            }

        }

    }

    public function is_image( $url ) {
        $pos = strrpos( $url, ".");
        if ($pos === false) {
            return false;
        }

        $ext = strtolower(trim(substr( $url, $pos)));
        $imgExts = array(".gif", ".jpg", ".jpeg", ".png", ".tiff", ".tif"); // this is far from complete but that's always going to be the case...
        if ( in_array($ext, $imgExts) ) {
            return true;
        }
        return false;
    }

    public function is_video( $url ) {
        $pos = strrpos( $url, ".");
        if ($pos === false) {
            return false;
        }

        $ext = strtolower(trim(substr( $url, $pos)));
        $videoExts = array(".mp4");
        if ( in_array($ext, $videoExts) ) {
            return true;
        }
        return false;
    }

    public function check_rights() {

        // Check nonce
        if( ! isset( $_POST['meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['meta_box_nonce'], 'meta_box_nonce' ) ) return false;

       // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return false;

        // Prevent quick edit from clearing custom fields
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return false;

        // Check the user's permissions.
        if ( ! current_user_can( 'edit_post', $post->ID ) ) return false;

        return true;

    }

    // Save custom fields
    public function save_meta_options() {

        // Get Post object
        global $post;

        if ( ! $this->check_rights() ) return;

        $post_type_slug = get_post_type( get_the_ID() );

        foreach ( $this->args as $cpt_name => $options ) {

            if ( $post_type_slug !== $cpt_name ) {
                continue;
            }

            foreach ( $options['fields'] as $name => $value ) {

                // 'text' | 'password' | 'textarea' | 'select' | 'radio' | 'checkbox'
                switch ( $value['type'] ) {

                    case 'checkbox':

                        if ( is_array( $_POST[$name] ) ) {

                            /**
                             * ToDos:
                             * - sanitize array each
                             */
                            update_post_meta( $post->ID, $name, $_POST[$name] );

                        } else {

                            // Sanitize user input.
                            $sanitized_value = $_POST[$name] ? 'yes' : 'no';

                            // Update the meta field in the database.
                            update_post_meta( $post->ID, $name, $sanitized_value );

                        }

                        break;
                    case 'textarea':
                        update_post_meta( $post->ID, $name, sanitize_textarea_field( $_POST[$name] ) );
                        break;

                    default:
                        update_post_meta( $post->ID, $name, sanitize_text_field( $_POST[$name] ) );
                        break;

                }

            }

        }

    }

}
endif;
