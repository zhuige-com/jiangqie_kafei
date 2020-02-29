<?php
if ( ! class_exists( 'Exopite_Post_Types' ) ) :

class Exopite_Post_Types {

    public $args = array();

    public function init() {

		/**
         * The problem with the initial activation code is that when the activation hook runs, it's after the init hook has run,
         * so hooking into init from the activation hook won't do anything.
         * You don't need to register the CPT within the activation function unless you need rewrite rules to be added
         * via flush_rewrite_rules() on activation. In that case, you'll want to register the CPT normally, via the
         * loader on the init hook, and also re-register it within the activation function and
         * call flush_rewrite_rules() to add the CPT rewrite rules.
         *
         * @link https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/issues/261
         */
		add_action( 'init', array( $this, 'create_custom_post_types' ) );

    }

    /**
     * Register custom post type
     *
     * @link https://codex.wordpress.org/Function_Reference/register_post_type
     */
    private function register_single_post_type( $fields ) {

        /**
		 * Labels used when displaying the posts in the admin and sometimes on the front end.  These
		 * labels do not cover post updated, error, and related messages.  You'll need to filter the
		 * 'post_updated_messages' hook to customize those.
		 */
        $labels = array(
            'name'                  => $fields['plural'],
            'singular_name'         => $fields['singular'],
            'menu_name'             => $fields['menu_name'],
            'new_item'              => sprintf( __( 'New %s', 'jiangqie-api' ), $fields['singular'] ),
            'add_new_item'          => sprintf( __( 'Add new %s', 'jiangqie-api' ), $fields['singular'] ),
            'edit_item'             => sprintf( __( 'Edit %s', 'jiangqie-api' ), $fields['singular'] ),
            'view_item'             => sprintf( __( 'View %s', 'jiangqie-api' ), $fields['singular'] ),
            'view_items'            => sprintf( __( 'View %s', 'jiangqie-api' ), $fields['plural'] ),
            'search_items'          => sprintf( __( 'Search %s', 'jiangqie-api' ), $fields['plural'] ),
            'not_found'             => sprintf( __( 'No %s found', 'jiangqie-api' ), strtolower( $fields['plural'] ) ),
            'not_found_in_trash'    => sprintf( __( 'No %s found in trash', 'jiangqie-api' ), strtolower( $fields['plural'] ) ),
            'all_items'             => sprintf( __( 'All %s', 'jiangqie-api' ), $fields['plural'] ),
            'archives'              => sprintf( __( '%s Archives', 'jiangqie-api' ), $fields['singular'] ),
            'attributes'            => sprintf( __( '%s Attributes', 'jiangqie-api' ), $fields['singular'] ),
            'insert_into_item'      => sprintf( __( 'Insert into %s', 'jiangqie-api' ), strtolower( $fields['singular'] ) ),
            'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'jiangqie-api' ), strtolower( $fields['singular'] ) ),

            /* Labels for hierarchical post types only. */
            'parent_item'           => sprintf( __( 'Parent %s', 'jiangqie-api' ), $fields['singular'] ),
            'parent_item_colon'     => sprintf( __( 'Parent %s:', 'jiangqie-api' ), $fields['singular'] ),

            /* Custom archive label.  Must filter 'post_type_archive_title' to use. */
			'archive_title'        => $fields['plural'],
        );

        $args = array(
            'labels'             => $labels,
            'description'        => ( isset( $fields['description'] ) ) ? $fields['description'] : '',
            'public'             => ( isset( $fields['public'] ) ) ? $fields['public'] : true,
            'publicly_queryable' => ( isset( $fields['publicly_queryable'] ) ) ? $fields['publicly_queryable'] : true,
            'exclude_from_search'=> ( isset( $fields['exclude_from_search'] ) ) ? $fields['exclude_from_search'] : false,
            'show_ui'            => ( isset( $fields['show_ui'] ) ) ? $fields['show_ui'] : true,
            'show_in_menu'       => ( isset( $fields['show_in_menu'] ) ) ? $fields['show_in_menu'] : true,
            'query_var'          => ( isset( $fields['query_var'] ) ) ? $fields['query_var'] : true,
            'show_in_admin_bar'  => ( isset( $fields['show_in_admin_bar'] ) ) ? $fields['show_in_admin_bar'] : true,
            'capability_type'    => ( isset( $fields['capability_type'] ) ) ? $fields['capability_type'] : 'post',
            'has_archive'        => ( isset( $fields['has_archive'] ) ) ? $fields['has_archive'] : true,
            'hierarchical'       => ( isset( $fields['hierarchical'] ) ) ? $fields['hierarchical'] : true,
            'supports'           => ( isset( $fields['supports'] ) ) ? $fields['supports'] : array(
                    'title',
                    'editor',
                    'excerpt',
                    'author',
                    'thumbnail',
                    'comments',
                    'trackbacks',
                    'custom-fields',
                    'revisions',
                    'page-attributes',
                    'post-formats',
            ),
            'menu_position'      => ( isset( $fields['menu_position'] ) ) ? $fields['menu_position'] : 21,
            'menu_icon'          => ( isset( $fields['menu_icon'] ) ) ? $fields['menu_icon']: 'dashicons-admin-generic',
            'show_in_nav_menus'  => ( isset( $fields['show_in_nav_menus'] ) ) ? $fields['show_in_nav_menus'] : true,
        );

        if ( isset( $fields['rewrite'] ) ) {

            /**
             *  Add $this->jiangqie_api as translatable in the permalink structure,
             *  to avoid conflicts with other plugins which may use customers as well.
             */
            $args['rewrite'] = $fields['rewrite'];
        }

        if ( $fields['custom_caps'] ) {

            /**
             * Provides more precise control over the capabilities than the defaults.  By default, WordPress
             * will use the 'capability_type' argument to build these capabilities.  More often than not,
             * this results in many extra capabilities that you probably don't need.  The following is how
             * I set up capabilities for many post types, which only uses three basic capabilities you need
             * to assign to roles: 'manage_examples', 'edit_examples', 'create_examples'.  Each post type
             * is unique though, so you'll want to adjust it to fit your needs.
             *
             * @link https://gist.github.com/creativembers/6577149
             * @link http://justintadlock.com/archives/2010/07/10/meta-capabilities-for-custom-post-types
             */
            $args['capabilities'] = array(

                // Meta capabilities
                'edit_post'                 => 'edit_' . strtolower( $fields['singular'] ),
                'read_post'                 => 'read_' . strtolower( $fields['singular'] ),
                'delete_post'               => 'delete_' . strtolower( $fields['singular'] ),

                // Primitive capabilities used outside of map_meta_cap():
                'edit_posts'                => 'edit_' . strtolower( $fields['plural'] ),
                'edit_others_posts'         => 'edit_others_' . strtolower( $fields['plural'] ),
                'publish_posts'             => 'publish_' . strtolower( $fields['plural'] ),
                'read_private_posts'        => 'read_private_' . strtolower( $fields['plural'] ),

                // Primitive capabilities used within map_meta_cap():
                'delete_posts'              => 'delete_' . strtolower( $fields['plural'] ),
                'delete_private_posts'      => 'delete_private_' . strtolower( $fields['plural'] ),
                'delete_published_posts'    => 'delete_published_' . strtolower( $fields['plural'] ),
                'delete_others_posts'       => 'delete_others_' . strtolower( $fields['plural'] ),
                'edit_private_posts'        => 'edit_private_' . strtolower( $fields['plural'] ),
                'edit_published_posts'      => 'edit_published_' . strtolower( $fields['plural'] ),
                'create_posts'              => 'edit_' . strtolower( $fields['plural'] )

            );

            /**
             * Adding map_meta_cap will map the meta correctly.
             * @link https://wordpress.stackexchange.com/questions/108338/capabilities-and-custom-post-types/108375#108375
             */
            $args['map_meta_cap'] = true;

            /**
             * Assign capabilities to users
             * Without this, users - also admins - can not see post type.
             */
            $this->assign_capabilities( $args['capabilities'], $fields['custom_caps_users'] );
        }

        register_post_type( $fields['slug'], $args );

        /**
         * Register Taxnonmies if any
         * @link https://codex.wordpress.org/Function_Reference/register_taxonomy
         */
        if ( isset( $fields['taxonomies'] ) && is_array( $fields['taxonomies'] ) ) {

            foreach ( $fields['taxonomies'] as $taxonomy ) {

                $this->register_single_post_type_taxnonomy( $taxonomy );

            }

        }

    }

    private function register_single_post_type_taxnonomy( $tax_fields ) {

        $labels = array(
            'name'                       => $tax_fields['plural'],
            'singular_name'              => $tax_fields['single'],
            'menu_name'                  => $tax_fields['plural'],
            'all_items'                  => sprintf( __( 'All %s' , 'jiangqie-api' ), $tax_fields['plural'] ),
            'edit_item'                  => sprintf( __( 'Edit %s' , 'jiangqie-api' ), $tax_fields['single'] ),
            'view_item'                  => sprintf( __( 'View %s' , 'jiangqie-api' ), $tax_fields['single'] ),
            'update_item'                => sprintf( __( 'Update %s' , 'jiangqie-api' ), $tax_fields['single'] ),
            'add_new_item'               => sprintf( __( 'Add New %s' , 'jiangqie-api' ), $tax_fields['single'] ),
            'new_item_name'              => sprintf( __( 'New %s Name' , 'jiangqie-api' ), $tax_fields['single'] ),
            'parent_item'                => sprintf( __( 'Parent %s' , 'jiangqie-api' ), $tax_fields['single'] ),
            'parent_item_colon'          => sprintf( __( 'Parent %s:' , 'jiangqie-api' ), $tax_fields['single'] ),
            'search_items'               => sprintf( __( 'Search %s' , 'jiangqie-api' ), $tax_fields['plural'] ),
            'popular_items'              => sprintf( __( 'Popular %s' , 'jiangqie-api' ), $tax_fields['plural'] ),
            'separate_items_with_commas' => sprintf( __( 'Separate %s with commas' , 'jiangqie-api' ), $tax_fields['plural'] ),
            'add_or_remove_items'        => sprintf( __( 'Add or remove %s' , 'jiangqie-api' ), $tax_fields['plural'] ),
            'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s' , 'jiangqie-api' ), $tax_fields['plural'] ),
            'not_found'                  => sprintf( __( 'No %s found' , 'jiangqie-api' ), $tax_fields['plural'] ),
        );

        $args = array(
        	'label'                 => $tax_fields['plural'],
        	'labels'                => $labels,
        	'hierarchical'          => ( isset( $tax_fields['hierarchical'] ) )          ? $tax_fields['hierarchical']          : true,
        	'public'                => ( isset( $tax_fields['public'] ) )                ? $tax_fields['public']                : true,
        	'show_ui'               => ( isset( $tax_fields['show_ui'] ) )               ? $tax_fields['show_ui']               : true,
        	'show_in_nav_menus'     => ( isset( $tax_fields['show_in_nav_menus'] ) )     ? $tax_fields['show_in_nav_menus']     : true,
        	'show_tagcloud'         => ( isset( $tax_fields['show_tagcloud'] ) )         ? $tax_fields['show_tagcloud']         : true,
        	'meta_box_cb'           => ( isset( $tax_fields['meta_box_cb'] ) )           ? $tax_fields['meta_box_cb']           : null,
        	'show_admin_column'     => ( isset( $tax_fields['show_admin_column'] ) )     ? $tax_fields['show_admin_column']     : true,
        	'show_in_quick_edit'    => ( isset( $tax_fields['show_in_quick_edit'] ) )    ? $tax_fields['show_in_quick_edit']    : true,
        	'update_count_callback' => ( isset( $tax_fields['update_count_callback'] ) ) ? $tax_fields['update_count_callback'] : '',
        	'show_in_rest'          => ( isset( $tax_fields['show_in_rest'] ) )          ? $tax_fields['show_in_rest']          : true,
        	'rest_base'             => $tax_fields['taxonomy'],
        	'rest_controller_class' => ( isset( $tax_fields['rest_controller_class'] ) ) ? $tax_fields['rest_controller_class'] : 'WP_REST_Terms_Controller',
        	'query_var'             => $tax_fields['taxonomy'],
        	'rewrite'               => ( isset( $tax_fields['rewrite'] ) )               ? $tax_fields['rewrite']               : true,
        	'sort'                  => ( isset( $tax_fields['sort'] ) )                  ? $tax_fields['sort']                  : '',
        );

        $args = apply_filters( $tax_fields['taxonomy'] . '_args', $args );

        register_taxonomy( $tax_fields['taxonomy'], $tax_fields['post_types'], $args );

    }

    /**
     * Assign capabilities to users
     *
     * @link https://codex.wordpress.org/Function_Reference/register_post_type
     * @link https://typerocket.com/ultimate-guide-to-custom-post-types-in-wordpress/
     */
    public function assign_capabilities( $caps_map, $users  ) {

        foreach ( $users as $user ) {

            $user_role = get_role( $user );

            foreach ( $caps_map as $cap_map_key => $capability ) {

                $user_role->add_cap( $capability );

            }

        }

    }

    /**
     * Create post types
     */
    public function create_custom_post_types() {

        if ( empty( $this->args ) ) return;

        foreach ( $this->args as $fields ) {

            $this->register_single_post_type( $fields );

        }

    }

    public function how_to_use() {

        /**
         * This is not all the fields, only what I find important. Feel free to change this function ;)
         *
         * @link https://codex.wordpress.org/Function_Reference/register_post_type
         */
        $this->args = array(

            array(
                /**
                 * Post type name/slug. Max of 20 characters! Uppercase and spaces not allowed.
                 */
                'slug'                  => 'test',

                'singular'              => 'Test',
                'plural'                => 'Tests',
                'menu_name'             => 'Tests',

                /**
                 * A short description of what your post type is. As far as I know, this isn't used anywhere
                 * in core WordPress.  However, themes may choose to display this on post type archives.
                 */
                'description'           => 'Tests',

                /**
                 * Whether the post type has an index/archive/root page like the "page for posts" for regular
                 * posts. If set to TRUE, the post type name will be used for the archive slug.  You can also
                 * set this to a string to control the exact name of the archive slug.
                 */
                'has_archive'           => true,

                /**
                 * Whether this post type should allow hierarchical (parent/child/grandchild/etc.) posts.
                 */
                'hierarchical'          => false,

                /**
                 * The URI to the icon to use for the admin menu item. There is no header icon argument, so
                 * you'll need to use CSS to add one.
                 *
                 * Dashicons:
                 * @link https://developer.wordpress.org/resource/dashicons/
                 */
                'menu_icon'             => 'dashicons-tag',

                /**
                 * How the URL structure should be handled with this post type.  You can set this to an
                 * array of specific arguments or true|false.  If set to FALSE, it will prevent rewrite
                 * rules from being created.
                 *
                 * Remove if not needed.
                 */
                'rewrite' => array(
                    /* The slug to use for individual posts of this type. */
                    'slug'       => 'tests', // string (defaults to the post type name)
                    /* Whether to show the $wp_rewrite->front slug in the permalink. */
                    'with_front' => true, // bool (defaults to TRUE)
                    /* Whether to allow single post pagination via the <!--nextpage--> quicktag. */
                    'pages'      => true, // bool (defaults to TRUE)
                    /* Whether to create feeds for this post type. */
                    'feeds'      => true, // bool (defaults to the 'has_archive' argument)
                    /* Assign an endpoint mask to this permalink. */
                    'ep_mask'    => EP_PERMALINK, // const (defaults to EP_PERMALINK)
                ),

                /**
                 * The position in the menu order the post type should appear. 'show_in_menu' must be true
                 * for this to work.
                 *
                 * 2 Dashboard
                 * 4 Separator
                 * 5 Posts
                 * 10 Media
                 * 15 Links
                 * 20 Pages
                 * 25 Comments
                 * 59 Separator
                 * 60 Appearance
                 * 65 Plugins
                 * 70 Users
                 * 75 Tools
                 * 80 Settings
                 * 99 Separator
                 *
                 * @link https://wordpress.stackexchange.com/questions/8779/placing-a-custom-post-type-menu-above-the-posts-menu-using-menu-position/65823#65823
                 */
                'menu_position'         => 21,

                /**
                 * Whether the post type should be used publicly via the admin or by front-end users.  This
                 * argument is sort of a catchall for many of the following arguments.  I would focus more
                 * on adjusting them to your liking than this argument.
                 */
                'public'                => true,

                /**
                 * Whether queries can be performed on the front end as part of parse_request().
                 */
                'publicly_queryable'    => true,

                /**
                 * Whether to exclude posts with this post type from front end search results.
                 */
                'exclude_from_search'   => true,
                /**
                 * Whether to generate a default UI for managing this post type in the admin. You'll have
                 * more control over what's shown in the admin with the other arguments.  To build your
                 * own UI, set this to FALSE.
                 *
                 * You can hide menu with caps too:
                 */
                // 'show_ui'               => ( current_user_can( 'read_test' ) ) ? true : false,
                'show_ui'               => true,

                /**
                 * Whether to show post type in the admin menu. 'show_ui' must be true for this to work.
                 */
                'show_in_menu'          => true,

                /**
                 * Sets the query_var key for this post type. If set to TRUE, the post type name will be used.
                 * You can also set this to a custom string to control the exact key.
                 */
                'query_var'             => true,

                /**
                 * Whether to make this post type available in the WordPress admin bar. The admin bar adds
                 * a link to add a new post type item.
                 */
                'show_in_admin_bar'     => true,

                /**
                 * Whether individual post type items are available for selection in navigation menus.
                 */
                'show_in_nav_menus'     => true,

                /**
                 * What WordPress features the post type supports.  Many arguments are strictly useful on
                 * the edit post screen in the admin.  However, this will help other themes and plugins
                 * decide what to do in certain situations.  You can pass an array of specific features or
                 * set it to FALSE to prevent any features from being added.  You can use
                 * add_post_type_support() to add features or remove_post_type_support() to remove features
                 * later.  The default features are 'title' and 'editor'.
                 *
                 * https://codex.wordpress.org/Function_Reference/post_type_supports
                 */
                'supports'              => array(
                    /* Post titles ($post->post_title). */
                    'title',
                    /* Post content ($post->post_content). */
                    'editor',
                    /* Post excerpt ($post->post_excerpt). */
                    'excerpt',
                    /* Post author ($post->post_author). */
                    'author',
                    /* Featured images (the user's theme must support 'post-thumbnails'). */
                    'thumbnail',
                    /* Displays comments meta box.  If set, comments (any type) are allowed for the post. */
                    'comments',
                    /* Displays meta box to send trackbacks from the edit post screen. */
                    'trackbacks',
                    /* Displays the Custom Fields meta box. Post meta is supported regardless. */
                    'custom-fields',
                    /* Displays the Revisions meta box. If set, stores post revisions in the database. */
                    'revisions',
                    /* Displays the Attributes meta box with a parent selector and menu_order input box. */
                    'page-attributes',
                    /* Displays the Format meta box and allows post formats to be used with the posts. */
                    'post-formats',
                ),

                //'capability_type'       => 'post',

                /**
                 * If you want to add custom capabilities to the post type.
                 */
                'custom_caps'           => true,

                /**
                 * Assign capabilities for roles.
                 */
                'custom_caps_users'     => array(
                    'administrator',
                    // 'editor',
                    // 'author',
                    // 'contributor',
                    // 'subscriber',
                ),

                'taxonomies'            => array(

                    array(
                        'taxonomy'              => 'test_category',
                        'plural'                => 'Test Categories',
                        'single'                => 'Test Category',
                        'post_types'            => array( 'test' ),
                    ),

                ),
            ),

            /*
            array(
                'slug'                  => 'test',
                'singular'              => 'Test',
                'plural'                => 'Tests',
                'menu_name'             => 'Tests',
                'description'           => 'Tests',
                'has_archive'           => true,
                'hierarchical'          => false,
                'menu_icon'             => 'dashicons-tag',
                'rewrite' => array(
                    'slug'                  => 'tests',
                    'with_front'            => true,
                    'pages'                 => true,
                    'feeds'                 => true,
                    'ep_mask'               => EP_PERMALINK,
                ),
                'menu_position'         => 21,
                'public'                => true,
                'publicly_queryable'    => true,
                'exclude_from_search'   => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'query_var'             => true,
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'supports'              => array(
                    'title',
                    'editor',
                    'excerpt',
                    'author',
                    'thumbnail',
                    'comments',
                    'trackbacks',
                    'custom-fields',
                    'revisions',
                    'page-attributes',
                    'post-formats',
                ),
                'custom_caps'           => true,
                'custom_caps_users'     => array(
                    'administrator',
                ),
                'taxonomies'            => array(
                    array(
                        'taxonomy'          => 'test_category',
                        'plural'            => 'Test Categories',
                        'single'            => 'Test Category',
                        'post_types'        => array( 'test' ),
                    ),
                ),
            ),
            */

        );
    }

}

endif;
