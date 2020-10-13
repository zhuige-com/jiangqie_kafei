<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * Copyright ️ 2020 www.jiangqie.com All rights reserved.
 */

class JiangQie_API_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $jiangqie_api    The ID of this plugin.
     */
    private $jiangqie_api;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /*************************************************************
     * ACCESS PLUGIN ADMIN PUBLIC METHODES FROM INSIDE
     *
     * @tutorial access_plugin_admin_public_methodes_from_inside.php
     */
    /**
     * Store plugin main class to allow public access.
     *
     * @since    20180622
     * @var object      The main class.
     */
    public $main;
    // ACCESS PLUGIN ADMIN PUBLIC METHODES FROM INSIDE

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $jiangqie_api       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    // public function __construct( $jiangqie_api, $version ) {

    // 	$this->jiangqie_api = $jiangqie_api;
    // 	$this->version = $version;

    // }

    /*************************************************************
     * ACCESS PLUGIN ADMIN PUBLIC METHODES FROM INSIDE
     *
     * @tutorial access_plugin_admin_public_methodes_from_inside.php
     */
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $jiangqie_api       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($jiangqie_api, $version, $plugin_main)
    {

        $this->jiangqie_api = $jiangqie_api;
        $this->version = $version;
        $this->main = $plugin_main;
    }
    // ACCESS PLUGIN ADMIN PUBLIC METHODES FROM INSIDE

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in JiangQie_API_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The JiangQie_API_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->jiangqie_api, plugin_dir_url(__FILE__) . 'css/jiangqie-api-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in JiangQie_API_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The JiangQie_API_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->jiangqie_api, plugin_dir_url(__FILE__) . 'js/jiangqie-api-admin.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->jiangqie_api . '_edit_extend', plugin_dir_url(__FILE__) . 'js/jiangqie-pro-edit-extend.js', array('quicktags'), $this->version, false);
    }

    public function get_all_emails()
    {

        $all_users = get_users();

        $user_email_list = array();

        foreach ($all_users as $user) {
            $user_email_list[esc_html($user->user_email)] = esc_html($user->display_name);
        }

        return $user_email_list;
    }

    public function test_sanitize_callback($val)
    {
        return str_replace('a', 'b', $val);
    }

    public function create_menu()
    {
        $result = get_pages(array('child_of' => 0, 'sort_column' => 'post_date', 'sort_order' => 'desc'));
        $pages = [];
        foreach ($result as $page) {
            $pages[$page->ID] = $page->post_title;
        }

        /**
         * Create a submenu page under Plugins.
         * Framework also add "Settings" to your plugin in plugins list.
         * @link https://github.com/JoeSz/Exopite-Simple-Options-Framework
         */
        $config_submenu = array(

            'type'              => 'menu',                          // Required, menu or metabox
            'id'                => $this->jiangqie_api,              // Required, meta box id, unique per page, to save: get_option( id )
            'parent'            => 'plugins.php',                   // Parent page of plugin menu (default Settings [options-general.php])
            'submenu'           => false,                            // Required for submenu
            'title'             => '酱茄Free小程序',                       // The title of the options page and the name in admin menu
            'capability'        => 'manage_options',                // The capability needed to view the page
            'plugin_basename'   =>  plugin_basename(plugin_dir_path(__DIR__) . $this->jiangqie_api . '.php'),
            'tabbed'            => true,
            'multilang'         => false,                        // To turn of multilang, default on.

        );

        /*
         * To add a metabox.
         * This normally go to your functions.php or another hook
         */
        $config_metabox = array(

            /*
             * METABOX
             */
            'type'              => 'metabox',                       // Required, menu or metabox
            'id'                => $this->jiangqie_api,              // Required, meta box id, unique, for saving meta: id[field-id]
            'post_types'        => array('test'),                 // Post types to display meta box
            // 'post_types'        => array( 'post', 'page' ),         // Could be multiple
            'context'           => 'advanced',                      // 	The context within the screen where the boxes should display: 'normal', 'side', and 'advanced'.
            'priority'          => 'default',                       // 	The priority within the context where the boxes should show ('high', 'low').
            'title'             => 'Demo Metabox',                  // The title of the metabox
            'capability'        => 'edit_posts',                    // The capability needed to view the page
            'tabbed'            => true,
            // 'multilang'         => false,                        // To turn of multilang, default off except if you have qTransalte-X.
            'options'           => 'simple',                        // Only for metabox, options is stored az induvidual meta key, value pair.
            /**
         * Simple options is stored az induvidual meta key, value pair, otherwise it is stored in an array.
         *
         * I implemented this option because it is possible to search in serialized (array) post meta:
         * @link https://wordpress.stackexchange.com/questions/16709/meta-query-with-meta-values-as-serialize-arrays
         * @link https://stackoverflow.com/questions/15056407/wordpress-search-serialized-meta-data-with-custom-query
         * @link https://www.simonbattersby.com/blog/2013/03/querying-wordpress-serialized-custom-post-data/
         *
         * but there is no way to sort them with wp_query or SQL.
         * @link https://wordpress.stackexchange.com/questions/87265/order-by-meta-value-serialized-array/87268#87268
         * "Not in any reliable way. You can certainly ORDER BY that value but the sorting will use the whole serialized string,
         * which will give * you technically accurate results but not the results you want. You can't extract part of the string
         * for sorting within the query itself. Even if you wrote raw SQL, which would give you access to database functions like
         * SUBSTRING, I can't think of a dependable way to do it. You'd need a MySQL function that would unserialize the value--
         * you'd have to write it yourself.
         * Basically, if you need to sort on a meta_value you can't store it serialized. Sorry."
         *
         * It is possible to get all required posts and store them in an array and then sort them as an array,
         * but what if you want multiple keys/value pair to be sorted?
         *
         * UPDATE
         * it is maybe possible:
         * @link http://www.russellengland.com/2012/07/how-to-unserialize-data-using-mysql.html
         * but it is waaay more complicated and less documented as meta query sort and search.
         * It should be not an excuse to use it, but it is not as reliable as it should be.
         *
         * @link https://wpquestions.com/Order_by_meta_key_where_value_is_serialized/7908
         * "...meta info serialized is not a good idea. But you really are going to lose the ability to query your
         * data in any efficient manner when serializing entries into the WP database.
         *
         * The overall performance saving and gain you think you are achieving by serialization is not going to be noticeable to
         * any major extent. You might obtain a slightly smaller database size but the cost of SQL transactions is going to be
         * heavy if you ever query those fields and try to compare them in any useful, meaningful manner.
         *
         * Instead, save serialization for data that you do not intend to query in that nature, but instead would only access in
         * a passive fashion by the direct WP API call get_post_meta() - from that function you can unpack a serialized entry
         * to access its array properties too."
         */

        );

        /**
         * Available fields:
         * - ACE field
         * - attached
         * - backup
         * - button
         * - botton_bar
         * - card
         * - checkbox
         * - color
         * - color_wp
         * - content
         * - date
         * - editor
         * - gallery
         * - group/accordion item
         * - hidden
         * - image
         * - image_select
         * - meta
         * - notice
         * - number
         * - password
         * - radio
         * - range
         * - select
         * - switcher
         * - tab
         * - tap_list
         * - text
         * - textarea
         * - typography
         * - upload
         * - video mp4/oembed
         */

        $content = '欢迎使用酱茄小程序开源版';
        $res = wp_remote_get("https://key.jiangqie.com/api/goods/description?goods_id=2", ['timeout' => 1]);
		if (!is_wp_error($res) && $res['response']['code'] == 200) {
			$data = json_decode($res['body'], TRUE);
			if ($data['code'] == 1) {
                $content = $data['data'];
			}
		} 

        $fields[] = array(
            'name'   => 'abstract',
            'title'  => '概要',
            'icon'   => 'fa fa-circle-o',
            'fields' => array(

                array(
                    'type'    => 'content',
                    'class'   => 'class-name', // for all fieds
                    'content' => $content,
                ),
                
            )
        );

        $fields[] = array(
            'name'   => 'basic',
            'title'  => '基础设置',
            'icon'   => 'fa fa-cog',
            'fields' => array(

                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'class'       => 'text-class',
                    'description' => '小程序标题',
                    'attributes'    => array(
                        'placeholder' => '请输入小程序标题',
                        'data-test'   => 'test',
                    ),
                    // 'sanitize'    => array($this, 'test_sanitize_callback'),
                ),

                array(
                    'id'    => 'logo',
                    'type'  => 'image',
                    'title' => 'LOGO',
                ),

                array(
                    'id'          => 'app_id',
                    'type'        => 'text',
                    'title'       => 'Appid',
                    'class'       => 'text-class',
                    'description' => '小程序 Appid',
                    'attributes'    => array(
                        'placeholder' => '请输入小程序 Appid',
                        'data-test'   => 'test',
                    ),
                    // 'sanitize'    => array($this, 'test_sanitize_callback'),
                ),

                array(
                    'id'          => 'app_secret',
                    'type'        => 'text',
                    'title'       => 'AppSecret',
                    'class'       => 'text-class',
                    'description' => '小程序 AppSecret',
                    'attributes'    => array(
                        'placeholder' => '请输入小程序 AppSecret',
                        'data-test'   => 'test',
                    ),
                    // 'sanitize'    => array($this, 'test_sanitize_callback'),
                ),

                array(
                    'id'          => 'hide_cat',
                    'type'        => 'text',
                    'title'       => '隐藏分类',
                    'before'      => '分类ID,英文逗号分隔',
                    'after'       => '例如：3,4,7',
                    'class'       => 'text-class',
                    'description' => '隐藏相应分类下的文章',
                ),

                array(
                    'id'      => 'switch_excerpt',
                    'type'    => 'switcher',
                    'title'   => '文章摘要',
                    'label'   => '文章列表中是否显示摘要?',
                    'default' => 'no',
                ),

                array(
                    'id'      => 'switch_comment',
                    'type'    => 'switcher',
                    'title'   => '评论',
                    'label'   => '是否开启评论功能?',
                    'default' => 'yes',
                ),

                array(
                    'id'      => 'switch_comment_verify',
                    'type'    => 'switcher',
                    'title'   => '评论审核',
                    'label'   => '评论是否需要审核?',
                    'default' => 'yes',
                ),

                array(
                    'id'    => 'default_thumbnail',
                    'type'  => 'image',
                    'title' => '默认微缩图',
                ),

                array(
                    'id'      => 'jiangqie_switch_stick',
                    'type'    => 'switcher',
                    'title'   => '置顶功能',
                    'label'   => '是否开启置顶功能',
                    'default' => 'no',
                ),
            )
        );

        $fields[] = array(
            'name'   => 'home',
            'title'  => '首页设置',
            'icon'   => 'fa fa-home',
            'fields' => array(

                array(
                    'id'          => 'home_top_nav',
                    'type'        => 'text',
                    'title'       => '顶部导航',
                    'before'      => '分类ID,英文逗号分隔',
                    'after'       => '例如：8,12,23',
                    'class'       => 'text-class',
                    'description' => '设置顶部导航显示的分类',
                    'attributes'    => array(
                        'placeholder' => '请输入分类ID,英文逗号分隔',
                        'data-test'   => 'test',
                    ),
                    // 'sanitize'    => array($this, 'test_sanitize_callback'),
                ),

                array(
                    'id'          => 'top_slide',
                    'type'        => 'text',
                    'title'       => '幻灯片',
                    'before'      => '文章ID,英文逗号分隔',
                    'after'       => '例如：8,12,23',
                    'class'       => 'text-class',
                    'description' => '设置首页幻灯片显示的文章',
                    'attributes'    => array(
                        'placeholder' => '请输入文章ID,英文逗号分隔',
                        'data-test'   => 'test',
                    ),
                    // 'sanitize'    => array($this, 'test_sanitize_callback'),
                ),

                array(
                    'type'    => 'group',
                    'id'      => 'home_icon_nav',
                    'title'   => esc_html__('导航项', 'jiangqie-api'),
                    'options' => array(
                        'repeater'          => true,
                        'accordion'         => true,
                        // 'accordion'         => false,
                        'button_title'      => esc_html__('添加', 'jiangqie-api'),
                        'group_title'       => esc_html__('导航项', 'jiangqie-api'),
                        'limit'             => 8,
                        'sortable'          => true,
                        'closed'            => false,
                    ),

                    'fields'  => array(

                        array(
                            'id'    => 'icon',
                            'type'  => 'image',
                            'title' => '图标',
                        ),

                        array(
                            'id'      => 'title',
                            'type'    => 'text',
                            'title'   => esc_html__('标题', 'jiangqie-api'),
                            'attributes' => array(
                                'data-title' => 'title',
                                'placeholder' => esc_html__('请输入标题', 'plugin-name'),
                            ),
                        ),

                        array(
                            'id'      => 'link',
                            'type'    => 'text',
                            'title'   => esc_html__('链接', 'jiangqie-api'),
                            'attributes' => array(
                                'data-title' => 'title',
                                'placeholder' => esc_html__('请输入链接', 'plugin-name'),
                            ),
                        ),

                        array(
                            'id'      => 'enable',
                            'type'    => 'switcher',
                            'title'   => '启用',
                            'label'   => '是否启用此项?',
                            'default' => 'yes',
                        ),

                    ),

                ),

                array(
                    'id'          => 'home_active',
                    'type'        => 'accordion',
                    'title'       => esc_html__('活动区域图', 'jiangqie-api'),
                    'options' => array(
                        'allow_all_open' => false,
                    ),

                    'sections' => array(
                        array(
                            'options' => array(
                                'icon'   => 'fa fa-star',
                                'title'  => '左图',
                                'closed' => false,
                            ),
                            'fields' => array(
                                array(
                                    'id'    => 'left_image',
                                    'type'  => 'image',
                                    'title' => '左图',
                                ),
                                array(
                                    'id'      => 'left_title',
                                    'type'    => 'text',
                                    'title'   => esc_html__('标题', 'jiangqie-api'),
                                    'attributes' => array(
                                        'data-title' => 'title',
                                        'placeholder' => esc_html__('请输入标题', 'plugin-name'),
                                    ),
                                ),
                                array(
                                    'id'      => 'left_link',
                                    'type'    => 'text',
                                    'title'   => esc_html__('链接', 'jiangqie-api'),
                                    'attributes' => array(
                                        'data-title' => 'title',
                                        'placeholder' => esc_html__('请输入链接', 'plugin-name'),
                                    ),
                                ),
                            ),
                        ),

                        array(
                            'options' => array(
                                'icon'   => 'fa fa-star',
                                'title'  => '右上图',
                            ),
                            'fields' => array(
                                array(
                                    'id'    => 'right_top_image',
                                    'type'  => 'image',
                                    'title' => '右上图',
                                ),
                                array(
                                    'id'      => 'right_top_title',
                                    'type'    => 'text',
                                    'title'   => esc_html__('标题', 'jiangqie-api'),
                                    'attributes' => array(
                                        'data-title' => 'title',
                                        'placeholder' => esc_html__('请输入标题', 'plugin-name'),
                                    ),
                                ),
                                array(
                                    'id'      => 'right_top_link',
                                    'type'    => 'text',
                                    'title'   => esc_html__('链接', 'jiangqie-api'),
                                    'attributes' => array(
                                        'data-title' => 'title',
                                        'placeholder' => esc_html__('请输入链接', 'plugin-name'),
                                    ),
                                ),
                            ),
                        ),

                        array(
                            'options' => array(
                                'icon'   => 'fa fa-star',
                                'title'  => '右下图',
                            ),
                            'fields' => array(
                                array(
                                    'id'    => 'right_down_image',
                                    'type'  => 'image',
                                    'title' => '右下图',
                                ),
                                array(
                                    'id'      => 'right_down_title',
                                    'type'    => 'text',
                                    'title'   => esc_html__('标题', 'jiangqie-api'),
                                    'attributes' => array(
                                        'data-title' => 'title',
                                        'placeholder' => esc_html__('请输入标题', 'plugin-name'),
                                    ),
                                ),
                                array(
                                    'id'      => 'right_down_link',
                                    'type'    => 'text',
                                    'title'   => esc_html__('链接', 'jiangqie-api'),
                                    'attributes' => array(
                                        'data-title' => 'title',
                                        'placeholder' => esc_html__('请输入链接', 'plugin-name'),
                                    ),
                                ),
                            ),
                        ),
                    ),

                ),

                array(
                    'id'          => 'home_hot',
                    'type'        => 'text',
                    'title'       => '首页热门',
                    'before'      => '文章ID,英文逗号分隔',
                    'after'       => '例如：8,12,23',
                    'class'       => 'text-class',
                    'description' => '设置首页热门文章',
                    'attributes'    => array(
                        'placeholder' => '请输入文章ID,英文逗号分隔',
                        'data-test'   => 'test',
                    ),
                    // 'sanitize'    => array($this, 'test_sanitize_callback'),
                ),

                //列表模式
                array(
                    'id'            => 'home_list_mode',
                    'type'          => 'select',
                    'title'         => '列表模式',
                    'description'   => '首页文章列表显示方式',
                    'options'       => array(
                        '3'         => '混合模式',
                        '1'         => '小图模式',
                        '2'         => '大图模式',
                    ),
                    'class'       => 'chosen width-150',
                ),
            )
        );

        $fields[] = array(
            'name'   => 'category',
            'title'  => '分类设置',
            'icon'   => 'fa fa-bars',
            'fields' => array(

                array(
                    'id'    => 'category_background',
                    'type'  => 'image',
                    'title' => '背景图',
                    'description' => '分类背景图',
                ),

                array(
                    'id'          => 'category_title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'class'       => 'text-class',
                    'description' => '分类标题',
                    'attributes'    => array(
                        'placeholder' => '请输入分类标题',
                        'data-test'   => 'test',
                    ),
                    // 'sanitize'    => array($this, 'test_sanitize_callback'),
                ),

                array(
                    'id'          => 'category_description',
                    'type'        => 'text',
                    'title'       => '描述',
                    'class'       => 'text-class',
                    'description' => '分类描述',
                    'attributes'    => array(
                        'placeholder' => '请输入分类描述',
                        'data-test'   => 'test',
                    ),
                    // 'sanitize'    => array($this, 'test_sanitize_callback'),
                ),
            )
        );

        $fields[] = array(
            'name'   => 'hot',
            'title'  => '热榜设置',
            'icon'   => 'fa fa-free-code-camp',
            'fields' => array(

                array(
                    'id'    => 'hot_background',
                    'type'  => 'image',
                    'title' => '背景图',
                    'description' => '热门背景图',
                ),

                array(
                    'id'          => 'hot_title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'class'       => 'text-class',
                    'description' => '热门标题',
                    'attributes'    => array(
                        'placeholder' => '请输入热门标题',
                        'data-test'   => 'test',
                    ),
                    // 'sanitize'    => array($this, 'test_sanitize_callback'),
                ),

                array(
                    'id'          => 'hot_description',
                    'type'        => 'text',
                    'title'       => '描述',
                    'class'       => 'text-class',
                    'description' => '热门描述',
                    'attributes'    => array(
                        'placeholder' => '请输入热门描述',
                        'data-test'   => 'test',
                    ),
                    // 'sanitize'    => array($this, 'test_sanitize_callback'),
                ),
            )
        );

        $fields[] = array(
            'name'   => 'profile',
            'title'  => '我的设置',
            'icon'   => 'fa fa-user-circle-o',
            'fields' => array(

                array(
                    'id'    => 'profile_background',
                    'type'  => 'image',
                    'title' => '顶部背景图',
                ),

                array(
                    'type'    => 'group',
                    'id'      => 'profile_menu',
                    'title'   => esc_html__('菜单项', 'jiangqie-api'),
                    'options' => array(
                        'repeater'          => true,
                        'accordion'         => true,
                        // 'accordion'         => false,
                        'button_title'      => esc_html__('添加', 'jiangqie-api'),
                        'group_title'       => esc_html__('菜单项', 'jiangqie-api'),
                        'limit'             => 50,
                        'sortable'          => true,
                        'closed'            => false,
                    ),

                    'fields'  => array(

                        array(
                            'id'             => 'tp',
                            'type'           => 'select',
                            'title' => '类型',
                            'options'        => array(
                                'views'     => '我的浏览',
                                'likes'     => '我的点赞',
                                'favorites' => '我的收藏',
                                'comments'  => '我的评论',
                                // 'about'     => '关于我们',
                                'feedback'  => '意见反馈',
                                'contact'   => '在线客服',
                                'clear'     => '清除缓存',
                                'split'     => '段分割线',
                                'link'      => '自定义链接',
                                'page'      => '自定义页面',
                            ),
                            'default_option' => '请选择',
                            'default'     => '0',
                            'class'       => 'chosen width-150',
                        ),

                        array(
                            'id'    => 'icon',
                            'type'  => 'image',
                            'title' => '图标',
                            'dependency' => array('tp', '!=', 'split'),
                        ),

                        array(
                            'id'      => 'title',
                            'type'    => 'text',
                            'title'   => '标题',
                            'dependency' => array('tp', '!=', 'split'),
                            'attributes' => array(
                                'data-title' => 'title',
                                'placeholder' => '请输入标题',
                            ),
                        ),

                        array(
                            'id'      => 'link',
                            'type'    => 'text',
                            'title'   => '链接',
                            'dependency' => array('tp', '==', 'link'),
                            'attributes' => array(
                                'data-title' => 'title',
                                'placeholder' => '请输入链接',
                            ),
                        ),

                        array(
                            'id'             => 'page_id',
                            'type'           => 'select',
                            'title' => '类型',
                            'options'        => $pages,
                            'dependency' => array('tp', '==', 'page'),
                            'default_option' => '请选择',
                            'default'     => '0',
                            'class'       => 'chosen width-150',
                        ),

                        array(
                            'id'      => 'line',
                            'type'    => 'switcher',
                            'title'   => '分割线',
                            'label'   => '是否显示分割线?',
                            'default' => 'yes',
                            'dependency' => array('tp', '!=', 'split'),
                        ),

                        array(
                            'id'      => 'enable',
                            'type'    => 'switcher',
                            'title'   => '启用',
                            'label'   => '是否启用此项?',
                            'default' => 'yes',
                        ),

                    ),

                )
            )
        );

        /**
         * instantiate your admin page
         */
        $options_panel = new Exopite_Simple_Options_Framework($config_submenu, $fields);
        $options_panel = new Exopite_Simple_Options_Framework($config_metabox, $fields);
    }

    /**
     * Add new image size for admin thumbnail.
     *
     * @link https://wordpress.stackexchange.com/questions/54423/add-image-size-in-a-plugin-i-created/304941#304941
     */
    public function add_thumbnail_size()
    {
        add_image_size('new_thumbnail_size', 60, 75, true);
    }

    /**************************************************************
     * ADD/REMOVE/REORDER/SORT CUSTOM POST TYPE LIST COLUMNS (test)
     *
     * @tutorial add_remove_reorder_sort_custom_post_type_list_columns_in_admin_area.php
     */
    /**
     * Modify columns in tests list in admin area.
     */
    public function manage_test_posts_columns($columns)
    {

        // Remove unnecessary columns
        unset($columns['author'],
        $columns['comments']);

        // Rename title and add ID and Address
        $columns['thumbnail'] = '';
        $columns['text_1'] = esc_attr__('Text', 'jiangqie-api');
        $columns['color_2'] = esc_attr__('Color', 'jiangqie-api');
        $columns['date_2'] = esc_attr__('Date', 'jiangqie-api');


        /**
         * Rearrange column order
         *
         * Now define a new order. you need to look up the column
         * names in the HTML of the admin interface HTML of the table header.
         *
         *     "cb" is the "select all" checkbox.
         *     "title" is the title column.
         *     "date" is the date column.
         *     "icl_translations" comes from a plugin (eg.: WPML).
         *
         * change the order of the names to change the order of the columns.
         *
         * @link http://wordpress.stackexchange.com/questions/8427/change-order-of-custom-columns-for-edit-panels
         */
        $customOrder = array('cb', 'thumbnail', 'title', 'text_1', 'color_2', 'date_2', 'icl_translations', 'date');

        /**
         * return a new column array to wordpress.
         * order is the exactly like you set in $customOrder.
         */
        foreach ($customOrder as $column_name)
            $rearranged[$column_name] = $columns[$column_name];

        return $rearranged;
    }

    // Populate new columns in customers list in admin area
    public function manage_posts_custom_column($column, $post_id)
    {

        // For array, not simple options
        // global $post;
        // $custom = get_post_custom();
        // $meta = maybe_unserialize( $custom[$this->jiangqie_api][0] );

        // Populate column form meta
        switch ($column) {

            case "thumbnail":
                echo '<a href="' . get_edit_post_link() . '">';
                echo get_the_post_thumbnail($post_id, array(60, 60));
                echo '</a>';
                break;
            case "text_1":
                // no break;
            case "color_2":
                // no break;
            case "date_2":
                echo get_post_meta($post_id, $column, true);
                break;
                // case "some_column":
                //     // For array, not simple options
                //     echo $meta["some_column"];
                //     break;

        }
    }

    public function add_style_to_admin_head()
    {
        global $post_type;
        if ('test' == $post_type) {
?>
            <style type="text/css">
                .column-thumbnail {
                    width: 80px !important;
                }

                .column-title {
                    width: 30% !important;
                }
            </style>
<?php
        }
    }

    /**
     * To sort, Exopite Simple Options Framework need 'options' => 'simple'.
     * Simple options is stored az induvidual meta key, value pair, otherwise it is stored in an array.
     *
     *
     * Meta key value paars need to sort as induvidual.
     *
     * I implemented this option because it is possible to search in serialized (array) post meta:
     * @link https://wordpress.stackexchange.com/questions/16709/meta-query-with-meta-values-as-serialize-arrays
     * @link https://stackoverflow.com/questions/15056407/wordpress-search-serialized-meta-data-with-custom-query
     * @link https://www.simonbattersby.com/blog/2013/03/querying-wordpress-serialized-custom-post-data/
     *
     * but there is no way to sort them with wp_query or SQL.
     * @link https://wordpress.stackexchange.com/questions/87265/order-by-meta-value-serialized-array/87268#87268
     * "Not in any reliable way. You can certainly ORDER BY that value but the sorting will use the whole serialized string,
     * which will give * you technically accurate results but not the results you want. You can't extract part of the string
     * for sorting within the query itself. Even if you wrote raw SQL, which would give you access to database functions like
     * SUBSTRING, I can't think of a dependable way to do it. You'd need a MySQL function that would unserialize the value--
     * you'd have to write it yourself.
     * Basically, if you need to sort on a meta_value you can't store it serialized. Sorry."
     *
     * It is possible to get all required posts and store them in an array and then sort them as an array,
     * but what if you want multiple keys/value pair to be sorted?
     *
     * UPDATE
     * it is maybe possible:
     * @link http://www.russellengland.com/2012/07/how-to-unserialize-data-using-mysql.html
     * but it is waaay more complicated and less documented as meta query sort and search.
     * It should be not an excuse to use it, but it is not as reliable as it should be.
     *
     * @link https://wpquestions.com/Order_by_meta_key_where_value_is_serialized/7908
     * "...meta info serialized is not a good idea. But you really are going to lose the ability to query your
     * data in any efficient manner when serializing entries into the WP database.
     *
     * The overall performance saving and gain you think you are achieving by serialization is not going to be noticeable to
     * any major extent. You might obtain a slightly smaller database size but the cost of SQL transactions is going to be
     * heavy if you ever query those fields and try to compare them in any useful, meaningful manner.
     *
     * Instead, save serialization for data that you do not intend to query in that nature, but instead would only access in
     * a passive fashion by the direct WP API call get_post_meta() - from that function you can unpack a serialized entry
     * to access its array properties too."
     */
    public function manage_sortable_columns($columns)
    {

        $columns['text_1'] = 'text_1';
        $columns['color_2'] = 'color_2';
        $columns['date_2'] = 'date_2';

        return $columns;
    }

    public function manage_posts_orderby($query)
    {

        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        /**
         * meta_types:
         * Possible values are 'NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED'.
         * Default value is 'CHAR'.
         *
         * @link https://codex.wordpress.org/Class_Reference/WP_Meta_Query
         */
        $columns = array(
            'text_1'  => 'char',
            'color_2' => 'char',
            'date_2'  => 'date',
        );

        foreach ($columns as $key => $type) {

            if ($key === $query->get('orderby')) {
                $query->set('orderby', 'meta_value');
                $query->set('meta_key', $key);
                $query->set('meta_type', $type);
                break;
            }
        }
    }
    // END ADD/REMOVE/REORDER/SORT CUSTOM POST TYPE LIST COLUMNS (test)

    /********************************************
     * RUN CODE ON PLUGIN UPGRADE AND ADMIN NOTICE
     *
     * @tutorial run_code_on_plugin_upgrade_and_admin_notice.php
     */
    /**
     * This function runs when WordPress completes its upgrade process
     * It iterates through each plugin updated to see if ours is included
     *
     * @param $upgrader_object Array
     * @param $options Array
     * @link https://catapultthemes.com/wordpress-plugin-update-hook-upgrader_process_complete/
     */
    public function upgrader_process_complete($upgrader_object, $options)
    {

        // If an update has taken place and the updated type is plugins and the plugins element exists
        if ($options['action'] == 'update' && $options['type'] == 'plugin' && isset($options['plugins'])) {

            // Iterate through the plugins being updated and check if ours is there
            foreach ($options['plugins'] as $plugin) {
                if ($plugin == JIANG_QIE_API_BASE_NAME) {

                    // Set a transient to record that our plugin has just been updated
                    set_transient('exopite_sof_updated', 1);
                    set_transient('exopite_sof_updated_message', esc_html__('Thanks for updating', 'exopite_sof'));
                }
            }
        }
    }

    /**
     * Show a notice to anyone who has just updated this plugin
     * This notice shouldn't display to anyone who has just installed the plugin for the first time
     */
    public function display_update_notice()
    {
        // Check the transient to see if we've just activated the plugin
        if (get_transient('exopite_sof_updated')) {

            // @link https://digwp.com/2016/05/wordpress-admin-notices/
            echo '<div class="notice notice-success is-dismissible"><p><strong>' . get_transient('exopite_sof_updated_message') . '</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';

            // Delete the transient so we don't keep displaying the activation message
            delete_transient('exopite_sof_updated');
            delete_transient('exopite_sof_updated_message');
        }
    }
    // RUN CODE ON PLUGIN UPGRADE AND ADMIN NOTICE
}
