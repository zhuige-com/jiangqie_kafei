<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright ️© 2020-2021 www.jiangqie.com All rights reserved.
 */

class JiangQie_API_Admin
{
    private $jiangqie_api;

    private $version;

    public $main;

    public function __construct($jiangqie_api, $version, $plugin_main)
    {
        $this->jiangqie_api = $jiangqie_api;
        $this->version = $version;
        $this->main = $plugin_main;
    }

    public function enqueue_styles()
    {
        wp_enqueue_style($this->jiangqie_api, plugin_dir_url(__FILE__) . 'css/jiangqie-api-admin.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script($this->jiangqie_api, plugin_dir_url(__FILE__) . 'js/jiangqie-api-admin.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->jiangqie_api . '_edit_extend', plugin_dir_url(__FILE__) . 'js/jiangqie-pro-edit-extend.js', array('quicktags'), $this->version, false);
    }

    public function create_menu()
    {
        $result = get_pages(array('child_of' => 0, 'sort_column' => 'post_date', 'sort_order' => 'desc'));
        $pages = [];
        foreach ($result as $page) {
            $pages[$page->ID] = $page->post_title;
        }

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

        $config_metabox = array(
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
        );

        $content = '欢迎使用酱茄小程序! <br/><br/> 微信客服：jianbing2011 (加开源群、问题咨询、项目定制、购买咨询) <br/><br/> <a href="https://www.jiangqie.com/xz" target="_blank">更多免费产品</a>';
	    $res = wp_remote_get("https://key.jiangqie.com/api/goods/description?id=jq_xcx_free", ['timeout' => 1]);
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
                ),

                array(
                    'id'    => 'logo',
                    'type'  => 'image',
                    'title' => 'LOGO',
                ),

                array(
                    'id'          => 'app_id',
                    'type'        => 'text',
                    'title'       => '微信 Appid',
                    'class'       => 'text-class',
                    'description' => '小程序 Appid',
                    'attributes'    => array(
                        'placeholder' => '微信小程序 Appid',
                        'data-test'   => 'test',
                    ),
                ),

                array(
                    'id'          => 'app_secret',
                    'type'        => 'text',
                    'title'       => '微信 AppSecret',
                    'class'       => 'text-class',
                    'description' => '小程序 AppSecret',
                    'attributes'    => array(
                        'placeholder' => '微信小程序 AppSecret',
                        'data-test'   => 'test',
                    ),
                ),

                array(
                    'id'          => 'bd_app_key',
                    'type'        => 'text',
                    'title'       => '百度 App Key',
                    'class'       => 'text-class',
                    'description' => '小程序 App Key',
                    'attributes'    => array(
                        'placeholder' => '百度小程序 App Key',
                        'data-test'   => 'test',
                    ),
                ),

                array(
                    'id'          => 'bd_app_secret',
                    'type'        => 'text',
                    'title'       => '百度 AppSecret',
                    'class'       => 'text-class',
                    'description' => '小程序 AppSecret',
                    'attributes'    => array(
                        'placeholder' => '百度小程序 AppSecret',
                        'data-test'   => 'test',
                    ),
                ),

                array(
                    'id'          => 'qq_app_id',
                    'type'        => 'text',
                    'title'       => 'QQ Appid',
                    'class'       => 'text-class',
                    'description' => '小程序 Appid',
                    'attributes'    => array(
                        'placeholder' => 'QQ小程序 Appid',
                        'data-test'   => 'test',
                    ),
                ),

                array(
                    'id'          => 'qq_app_secret',
                    'type'        => 'text',
                    'title'       => 'QQ AppSecret',
                    'class'       => 'text-class',
                    'description' => '小程序 AppSecret',
                    'attributes'    => array(
                        'placeholder' => 'QQ小程序 AppSecret',
                        'data-test'   => 'test',
                    ),
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
                    'id'          => 'home_hot_search',
                    'type'        => 'text',
                    'title'       => '热门搜索词',
                    'before'      => '搜索词,英文逗号分隔',
                    'after'       => '例如：酱茄,美食',
                    'class'       => 'text-class',
                    'description' => '设置热门搜索词',
                ),

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

        $options_panel = new Exopite_Simple_Options_Framework($config_submenu, $fields);
        $options_panel = new Exopite_Simple_Options_Framework($config_metabox, $fields);
    }

    public function admin_init()
	{
        $this->handle_external_redirects();
    }

    public function admin_menu()
	{
        remove_submenu_page('jiangqie-api', 'jiangqie-api');
        add_submenu_page('jiangqie-api', '', '安装文档', 'manage_options', 'jiangqie_xcx_free_setup', array( &$this, 'handle_external_redirects' ) );
        add_submenu_page('jiangqie-api', '', '新版下载', 'manage_options', 'jiangqie_xcx_free_upgrade', array( &$this, 'handle_external_redirects' ) );
    }

    public function handle_external_redirects()
    {
      if (empty($_GET['page'])) {
        return;
      }

      if ('jiangqie_xcx_free_setup' === $_GET['page']) {
        wp_redirect('https://www.jiangqie.com/ky/4655.html');
        die;
      }

      if ('jiangqie_xcx_free_upgrade' === $_GET['page']) {
        wp_redirect('https://www.jiangqie.com/ky/4639.html');
        die;
      }
    }

}
