<?php

/*
 * 酱茄小程序开源版
 * Author: 追格
 * Help document: https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright ️© 2020-2023 www.zhuige.com All rights reserved.
 */

class JiangQie_API_Post_Controller extends JiangQie_API_Base_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = 'posts';
	}

	public function register_routes()
	{
		//最新文章
		register_rest_route($this->namespace, '/' . $this->module . '/last', [
			[
				'callback' => [$this, 'get_last_posts'],
				'args' => $this->params_paging(),
				'permission_callback' => '__return_true',
			]
		]);

		//获取某个分类下的文章
		register_rest_route($this->namespace, '/' . $this->module . '/category', [
			[
				'callback' => [$this, 'get_category_posts'],
				'args' => $this->params_paging(),
				'permission_callback' => '__return_true',
			]
		]);

		//获取某个TAG下的文章
		register_rest_route($this->namespace, '/' . $this->module . '/tag', [
			[
				'callback' => [$this, 'get_tag_posts'],
				'args' => $this->params_paging(),
				'permission_callback' => '__return_true',
			]
		]);

		//搜索
		register_rest_route($this->namespace, '/' . $this->module . '/search', [
			[
				'callback' => [$this, 'get_search_posts'],
				'args' => $this->params_paging(),
				'permission_callback' => '__return_true',
			]
		]);

		//热门搜索
		register_rest_route($this->namespace, '/' . $this->module . '/search/hot', [
			[
				'callback' => [$this, 'get_search_hot'],
				'args' => $this->params_paging(),
				'permission_callback' => '__return_true',
			]
		]);

		//文章详情
		register_rest_route($this->namespace, '/' . $this->module . '/detail', [
			[
				'callback' => [$this, 'get_post_detail'],
				'args' => [
					'post_id' => [
						'default' => 0,
						'description' => '文章ID',
						'type' => 'integer',
					],
					'token' => [
						'default' => '',
						'description' => '用户token',
						'type' => 'string',
					],
				],
				'permission_callback' => '__return_true',
			]
		]);

		//页面详情
		register_rest_route($this->namespace, '/' . $this->module . '/page', [
			[
				'callback' => [$this, 'get_post_page'],
				'args' => [
					'page_id' => [
						'default' => 0,
						'description' => '页面ID',
						'type' => 'integer',
					]
				],
				'permission_callback' => '__return_true',
			]
		]);

		//热门 浏览数[views] 点赞数[likes] 评论数[commnets]
		register_rest_route($this->namespace, '/' . $this->module . '/hot', [
			[
				'callback' => [$this, 'get_hot_posts'],
				'args' => array_merge($this->params_paging(), [
					'sort' => [
						'default' => 'views',
						'description' => '热门 浏览数[views] 点赞数[likes] 评论数[commnets] 收藏[favorites]',
						'type' => 'string',
					]
				]),
				'permission_callback' => '__return_true',
			]
		]);

		//我的文章 浏览数[views] 点赞数[likes] 评论数[commnets] 收藏[favorite]
		register_rest_route($this->namespace, '/' . $this->module . '/my', [
			[
				'callback' => [$this, 'get_my_posts'],
				'args' => array_merge($this->params_paging(), [
					'track' => [
						'default' => 'views',
						'description' => '我的文章 浏览数[views] 点赞数[likes] 评论数[commnets] 收藏[favorites]',
						'type' => 'string',
					],
					'token' => [
						'default' => '',
						'description' => '用户token',
						'type' => 'string',
					],
				]),
				'permission_callback' => '__return_true',
			]
		]);

		//微信二维码
		register_rest_route($this->namespace, '/' . $this->module . '/wxacode', [
			[
				'callback' => [$this, 'get_wxacode'],
				'permission_callback' => '__return_true',
			]
		]);


		//百度二维码
		register_rest_route($this->namespace, '/' . $this->module . '/bdacode', [
			[
				'callback' => [$this, 'get_bdacode'],
				'permission_callback' => '__return_true',
			]
		]);

		//QQ二维码
		register_rest_route($this->namespace, '/' . $this->module . '/qqacode', [
			[
				'callback' => [$this, 'get_qqacode'],
				'permission_callback' => '__return_true',
			]
		]);
	}

	/**
	 * 按【时间倒序】获取文章列表
	 */
	public function get_last_posts($request)
	{
		$offset = (int)($this->param_value($request, 'offset', 0));

		$args = [
			'posts_per_page' => JiangQie_API::POSTS_PER_PAGE,
			'offset' => $offset,
			'orderby' => 'date',
		];

		$hide_cat = JiangQie_API::option_value('hide_cat');
		if (!empty($hide_cat)) {
			//$args['category__not_in'] = explode(',', $hide_cat);
			$args['category__not_in'] = $hide_cat;
		}

		$posts = $this->get_posts($args, true);
		return $this->make_success($posts);
	}

	/**
	 * 搜索文章
	 */
	public function get_search_posts($request)
	{
		$offset = (int)($this->param_value($request, 'offset', 0));
		$search = $this->param_value($request, 'search', '');

		if (empty($search)) {
			return $this->make_error('缺少参数');
		}

		if (!$this->msg_sec_check($search)) {
			return $this->make_error('请勿搜索敏感信息');
		}

		global $wpdb;
		$table_post_search = $wpdb->prefix . 'jiangqie_post_search';
		$times = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT times FROM `$table_post_search` WHERE search=%s", $search
			)
		);
		if (empty($times)) {
			$wpdb->insert($table_post_search, [
				'search' => $search,
				'times' => 1,
			]);
		} else {
			$wpdb->update($table_post_search, ['times' => $times + 1], ['search' => $search]);
		}
		
		$args = [
			'posts_per_page' => JiangQie_API::POSTS_PER_PAGE,
			'offset' => $offset,
			'orderby' => 'date',
			's' => $search,
			'post_type' => ['post']
		];

		$hide_cat = JiangQie_API::option_value('hide_cat');
		if (!empty($hide_cat)) {
			//$args['category__not_in'] = explode(',', $hide_cat);
			$args['category__not_in'] = $hide_cat;
		}

		$posts = $this->get_posts($args);
		return $this->make_success($posts);
	}

	/**
	 * 热门搜索
	 */
	public function get_search_hot($request)
	{
		$home_hot_search = Jiangqie_Api::option_value('home_hot_search');
		if (!$home_hot_search) {
			global $wpdb;
			$table_post_search = $wpdb->prefix . 'jiangqie_post_search';
			$result = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT search FROM `$table_post_search` ORDER BY times DESC LIMIT %d, %d", 0, 10
				)
			);
			$searchs = array_column($result, 'search');
		} else {
			$searchs = explode(',', $home_hot_search);
		}

		return $this->make_success($searchs);
	}
	
	/**
	 * 获取文章详情
	 */
	public function get_post_detail($request)
	{
		$post_id = (int)($this->param_value($request, 'post_id'));
		if (!$post_id) {
			return $this->make_error('缺少参数');
		}

		$postObj = get_post($post_id);
		$post = [
			'id' => $postObj->ID,
			'time' => $postObj->post_date,
			'title' => $postObj->post_title,
			'content' => apply_filters('the_content', $postObj->post_content),
			'comment_count' => $postObj->comment_count,
			'thumbnail' => apply_filters('jiangqie_post_thumbnail', $postObj->ID)
		];
		
		if ($postObj->post_excerpt) {
			$post['excerpt'] = html_entity_decode(wp_trim_words($postObj->post_excerpt, 100, '...'));
		} else {
			$post['excerpt'] = html_entity_decode(wp_trim_words($post['content'], 100, '...'));
		}

		//查询tag
		$tags = get_the_tags($post_id);
		if (!$tags) {
			$post['tags'] = [];
		} else {
			$post['tags'] = apply_filters('jiangqie_tag_for_detail', $tags);
		}

		//查询分类
		$cats = get_the_category($post_id);
		$post['cats'] = [];
		foreach ($cats as $cat) {
			$post['cats'][] = [
				'id' => $cat->term_id,
				'name' => $cat->name,
			];
		}

		//美化时间
		$post['time'] = $this->time_beautify($post['time']);

		//处理文章浏览数
		$post_views = (int) get_post_meta($post_id, "views", true);
		if (!update_post_meta($post_id, 'views', ($post_views + 1))) {
			add_post_meta($post_id, 'views', 1, true);
		}
		$post['views'] = $post_views + 1;

		//点赞数
		// $post['likes'] = (int) get_post_meta($post_id, "likes", true);

		//点赞列表
		global $wpdb;
		$table_post_like = $wpdb->prefix . 'jiangqie_post_like';
		$users = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT user_id FROM `$table_post_like` WHERE post_id=%d ORDER BY id DESC", $post_id
			)
		);
		$post['like_list'] = [];
		if (!empty($users)) {
			foreach ($users as $user) {
				$post['like_list'][] = get_user_meta($user->user_id, 'jiangqie_avatar', true);
			}
		}

		//收藏数
		$post['favorites'] = (int) get_post_meta($post_id, "favorites", true);

		//能否评论
		$post['switch_comment'] = JiangQie_API::option_value('switch_comment') ? 1 : 0;

		//用户数据
		$user = [];
		$user_id = $this->check_login($request);
		if ($user_id) {
			$table_post_like = $wpdb->prefix . 'jiangqie_post_like';
			$post_like_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT id FROM `$table_post_like` WHERE user_id=%d AND post_id=%d", $user_id, $post_id
				)
			);
			$user['islike'] = $post_like_id ? 1 : 0;

			$table_post_favorite = $wpdb->prefix . 'jiangqie_post_favorite';
			$post_favorite_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT id FROM `$table_post_favorite` WHERE user_id=%d AND post_id=%d", $user_id, $post_id
				)
			);
			$user['isfavorite'] = $post_favorite_id ? 1 : 0;

			//添加文章浏览记录
			$table_post_view = $wpdb->prefix . 'jiangqie_post_view';
			$post_view_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT id FROM `$table_post_view` WHERE user_id=%d AND post_id=%d", $user_id, $post_id
				)
			);
			if (!$post_view_id) {
				$wpdb->insert($table_post_view, [
					'user_id' => $user_id,
					'post_id' => $post_id,
				]);
			}
		}
		$post['user'] = $user;

		//广告设置
		$wx_ad_top = JiangQie_API::option_value('wx_ad_article_top');
		if ($wx_ad_top && $wx_ad_top['switch'] && $wx_ad_top['adid']) {
			$post['wx_ad_top'] = $wx_ad_top['adid'];
		} else {
			$post['wx_ad_top'] = false;
		}

		$wx_ad_bottom = JiangQie_API::option_value('wx_ad_article_bottom');
		if ($wx_ad_bottom && $wx_ad_bottom['switch'] && $wx_ad_bottom['adid']) {
			$post['wx_ad_bottom'] = $wx_ad_bottom['adid'];
		} else {
			$post['wx_ad_bottom'] = false;
		}

		//上一篇 下一篇
		if (JiangQie_API::option_value('switch_pre_next')) {
			$post['pre_next'] = true;
			$GLOBALS['post'] = $postObj;
			$pre_post = get_previous_post();
			$post['pre'] = $pre_post ? $pre_post->ID : false;
			$next_post = get_next_post();
			$post['next'] = $next_post ? $next_post->ID: false;
		} else {
			$post['pre_next'] = false;
		}

		return $this->make_success($post);
	}

	/**
	 * 获取页面详情
	 */
	public function get_post_page($request)
	{
		$page_id = (int)($this->param_value($request, 'page_id'));
		if (!$page_id) {
			return $this->make_error('缺少参数');
		}

		global $wpdb;
		$table_post = $wpdb->prefix . 'posts';
		$result = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT post_title, post_content FROM `$table_post` WHERE ID=%d", $page_id
			)
		);
		if (!$result) {
			return $this->make_error('未找到文章');
		}
		$page['title'] = $result->post_title;
		$page['content'] = apply_filters('the_content', $result->post_content);

		return $this->make_success($page);
	}

	/**
	 * 获取某一TAG下的文章
	 */
	public function get_tag_posts($request)
	{
		$offset = (int)($this->param_value($request, 'offset', 0));
		$tag_id = (int)($this->param_value($request, 'tag_id', 0));

		$args = [
			'posts_per_page' => JiangQie_API::POSTS_PER_PAGE,
			'offset' => $offset,
			'ignore_sticky_posts' => 1,
			'orderby' => 'date',
			'tag_id' => $tag_id
		];

		$posts = $this->get_posts($args);
		return $this->make_success($posts);
	}

	/**
	 * 获取某一分类下的文章
	 */
	public function get_category_posts($request)
	{
		$offset = (int)($this->param_value($request, 'offset', 0));
		$cat_id = (int)($this->param_value($request, 'cat_id', 0));

		$args = [
			'posts_per_page' => JiangQie_API::POSTS_PER_PAGE,
			'offset' => $offset,
			'orderby' => 'date',
			'cat' => $cat_id
		];

		$posts = $this->get_posts($args);
		return $this->make_success($posts);
	}

	/**
	 * 热门 浏览数[views] 点赞数[likes] 评论数[commnets]
	 */
	public function get_hot_posts($request)
	{
		$offset = (int)($this->param_value($request, 'offset', 0));

		$args = [
			'posts_per_page' => JiangQie_API::POSTS_PER_PAGE,
			'offset' => $offset,
			'order' => 'DESC',
			'ignore_sticky_posts' => 1
		];

		$hide_cat = JiangQie_API::option_value('hide_cat');
		if (!empty($hide_cat)) {
			//$args['category__not_in'] = explode(',', $hide_cat);
			$args['category__not_in'] = $hide_cat;
		}

		$sort = $this->param_value($request, 'sort', 'views');
		if ($sort == 'views' || $sort == 'likes' || $sort == 'favorites') {
			$args['meta_key'] = $sort;
			$args['orderby'] = 'meta_value_num';
		} else {
			$args['orderby'] = 'comment_count';
		}

		$query = new WP_Query();
		$result = $query->query($args);

		$posts = apply_filters('jiangqie_post_for_list', $result);

		foreach ($posts as &$post) {
			//查询tag
			$tags = get_the_tags($post['id']);
			if (!$tags) {
				$post['tags'] = [];
			} else {
				$post['tags'] = apply_filters('jiangqie_tag_for_list', $tags);
			}

			if ($sort == 'views' || $sort == 'likes' || $sort == 'favorites') {
				$post[$sort] = (int)get_post_meta($post['id'], $sort, true);
			}

			//美化时间
			$post['time'] = $this->time_beautify($post['time']);
		}
		return $this->make_success($posts);
	}

	/**
	 * 我的文章 浏览数[views] 点赞数[likes] 评论数[commnets] 收藏[favorite]
	 */
	public function get_my_posts($request)
	{
		$user_id = $this->check_login($request);
		if (!$user_id) {
			return $this->make_error('还没有登陆', -1);
		}

		$track = $this->param_value($request, 'track', 'views');
		if (empty($track)) {
			return $this->make_error('缺少参数');
		}

		$offset = (int)($this->param_value($request, 'offset', 0));

		global $wpdb;
		if ($track == 'views') {
			$table_name = $wpdb->prefix . 'jiangqie_post_view';
			$field = 'post_id';
			$orderby = 'id';
		} else if ($track == 'likes') {
			$table_name = $wpdb->prefix . 'jiangqie_post_like';
			$field = 'post_id';
			$orderby = 'id';
		} else if ($track == 'comments') {
			$table_name = $wpdb->prefix . 'comments';
			$field = 'comment_post_ID';
			$orderby = 'comment_ID';
		} else if ($track == 'favorites') {
			$table_name = $wpdb->prefix . 'jiangqie_post_favorite';
			$field = 'post_id';
			$orderby = 'id';
		}
		
		$per_page_count = JiangQie_API::POSTS_PER_PAGE;
		$post_ids = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT distinct $field FROM `$table_name` WHERE user_id=%d ORDER BY $orderby DESC LIMIT %d, %d",
				$user_id, $offset, $per_page_count
			)
		);
		if (empty($post_ids)) {
			return $this->make_success([]);
		}

		$args = [
			'post__in' => array_column($post_ids, $field),
			'orderby' => 'post__in',
			'posts_per_page' => $per_page_count,
			'ignore_sticky_posts' => 1,
		];

		$posts = $this->get_posts($args);
		return $this->make_success($posts);
	}

	/**
	 * 处理文章
	 */
	private function get_posts($args, $qstick=false)
	{
		if ($qstick && JiangQie_API::option_value('jiangqie_switch_stick')) {
			//第一页获取置顶帖子
			if ($args['offset'] == 0) {
				$posts_stick = $this->_getStickPosts($args);
			} else {
				$posts_stick = [];
			}
			$posts_stick = apply_filters('jiangqie_post_for_list', $posts_stick);
			foreach ($posts_stick as &$post) {
				$post['stick'] = 1;
			}

			$args['post__not_in'] = get_option('sticky_posts');
			$query = new WP_Query();
			$posts_common = $query->query($args);
			$posts_common = apply_filters('jiangqie_post_for_list', $posts_common);

			$posts = array_merge($posts_stick, $posts_common);
		} else {
			$query = new WP_Query();
			$result = $query->query($args);

			$posts = apply_filters('jiangqie_post_for_list', $result);
		}

		foreach ($posts as &$post) {
			//查询tag
			$tags = get_the_tags($post['id']);
			if (!$tags) {
				$post['tags'] = [];
			} else {
				$post['tags'] = apply_filters('jiangqie_tag_for_list', $tags);
			}

			//美化时间
			$post['time'] = $this->time_beautify($post['time']);

			$post['views'] = (int)get_post_meta($post['id'], 'views', true);
		}

		return $posts;
	}

	/**
	 * 获取微信小程序码
	 */
	public function get_wxacode($request)
	{
		$post_id = (int)($this->param_value($request, 'post_id', 0));
		if (!$post_id) {
			return $this->make_error('缺少参数');
		}

		$post_type = get_post_type($post_id);
		if ($post_type != 'post') {
			return $this->make_error('暂不支持');
		}

		$uploads = wp_upload_dir();
		$qrcode_path = $uploads['basedir'] . '/jiangqie_wxacode/';
		if (!is_dir($qrcode_path)) {
			mkdir($qrcode_path, 0755);
		}

		$qrcode = $qrcode_path . $post_type . '-' . $post_id . '.png';
		$qrcode_link = $uploads['baseurl'] . '/jiangqie_wxacode/' . $post_type . '-' . $post_id . '.png';
		if (is_file($qrcode)) {
			return $this->make_success($qrcode_link);
		}

		$wx_session = JiangQie_API::get_wx_token();
		$access_token = $wx_session['access_token'];
		if (empty($access_token)) {
			return $this->make_error('获取二维码失败');
		}

		$api = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $access_token;

		$color = array(
			"r" => "0",  //这个颜色码自己到Photoshop里设
			"g" => "0",  //这个颜色码自己到Photoshop里设
			"b" => "0",  //这个颜色码自己到Photoshop里设
		);

		$page = 'pages/article/article';

		$data = array(
			'scene' => $post_id, //自定义信息，可以填写诸如识别用户身份的字段，注意用中文时的情况
			'page' => $page, // 前端传过来的页面path,不能为空，最大长度 128 字节
			'width' => 200, // 设置二维码尺寸,二维码的宽度
			'auto_color' => false, // 自动配置线条颜色，如果颜色依然是黑色，则说明不建议配置主色调
			'line_color' => $color, // auth_color 为 false 时生效，使用 rgb 设置颜色 例如 {"r":"xxx","g":"xxx","b":"xxx"},十进制表示
			'is_hyaline' => true, // 是否需要透明底色， is_hyaline 为true时，生成透明底色的小程序码
		);

		$args = array(
			'method'  => 'POST',
			'body' 	  => wp_json_encode($data),
			'headers' => array(),
			'cookies' => array()
		);

		$remote = wp_remote_post($api, $args);
		if (is_wp_error($remote)) {
			return $this->make_error('系统异常');
		}

		$content = wp_remote_retrieve_body($remote);
		if (strstr($content, 'errcode') !== false || strstr($content, 'errmsg') !== false) {
			// $json = json_decode($content, TRUE);
			// return $this->make_error($json['errmsg']);
			return $this->make_success(plugins_url('images/wxacode.jpg', dirname(__FILE__)));
		}

		//输出二维码
		file_put_contents($qrcode, $content);

		//同步到媒体库
		$res = jiangqie_free_import_image2attachment($qrcode, $post_id, 'current', true);
		if (!is_wp_error($res)) {
			$qrcode_link = $uploads['baseurl'] . '/jiangqie_wxacode/' . $res;
		}

		return $this->make_success($qrcode_link);
	}

	/**
	 * 获取百度小程序码
	 */
	public function get_bdacode($request)
	{
		$post_id = (int)($this->param_value($request, 'post_id', 0));
		if (!$post_id) {
			return $this->make_error('缺少参数');
		}

		$post_type = get_post_type($post_id);
		if ($post_type != 'post') {
			return $this->make_error('暂不支持');
		}

		$uploads = wp_upload_dir();
		$qrcode_path = $uploads['basedir'] . '/jiangqie_bdacode/';
		if (!is_dir($qrcode_path)) {
			mkdir($qrcode_path, 0755);
		}

		$qrcode = $qrcode_path . $post_type . '-' . $post_id . '.png';
		$qrcode_link = $uploads['baseurl'] . '/jiangqie_bdacode/' . $post_type . '-' . $post_id . '.png';
		if (is_file($qrcode)) {
			return $this->make_success($qrcode_link);
		}

		$wx_session = JiangQie_API::get_bd_token();
		$access_token = $wx_session['access_token'];
		if (empty($access_token)) {
			return $this->make_error('获取二维码失败');
		}

		$api = 'https://openapi.baidu.com/rest/2.0/smartapp/qrcode/getunlimited?access_token=' . $access_token;

		$data = array(
			'path' => 'pages/article/article?post_id=' . $post_id,
			// 'width' => 430, 尺寸 默认430
			// 'mf' => 1 是否包含logo 1001不包含 默认包含
		);

		$args = array(
			'method'  => 'POST',
			'body' 	  => $data,
			'headers' => array(),
			'cookies' => array()
		);

		$remote = wp_remote_post($api, $args);
		if (is_wp_error($remote)) {
			return $this->make_error('系统异常');
		}

		$content = wp_remote_retrieve_body($remote);
		if (strstr($content, 'errno') !== false || strstr($content, 'errmsg') !== false) {
			return $this->make_success(plugins_url('images/bdacode.jpg', dirname(__FILE__)));
		}

		//输出二维码
		file_put_contents($qrcode, $content);

		//同步到媒体库
		$res = jiangqie_free_import_image2attachment($qrcode, $post_id, 'current', true);
		if (!is_wp_error($res)) {
			$qrcode_link = $uploads['baseurl'] . '/jiangqie_bdacode/' . $res;
		}

		return $this->make_success($qrcode_link);
	}

	/**
	 * 获取QQ小程序码
	 */
	public function get_qqacode($request)
	{
		$post_id = (int)($this->param_value($request, 'post_id', 0));
		if (!$post_id) {
			return $this->make_error('缺少参数');
		}

		$post_type = get_post_type($post_id);
		if ($post_type != 'post') {
			return $this->make_error('暂不支持');
		}

		$uploads = wp_upload_dir();
		$qrcode_path = $uploads['basedir'] . '/jiangqie_qqacode/';
		if (!is_dir($qrcode_path)) {
			mkdir($qrcode_path, 0755);
		}

		$qrcode = $qrcode_path . $post_type . '-' . $post_id . '.png';
		$qrcode_link = $uploads['baseurl'] . '/jiangqie_qqacode/' . $post_type . '-' . $post_id . '.png';
		if (is_file($qrcode)) {
			return $this->make_success($qrcode_link);
		}

		$qq_session = JiangQie_API::get_qq_token();
		$access_token = $qq_session['access_token'];
		if (empty($access_token)) {
			return $this->make_error('获取二维码失败');
		}

		$api = 'https://api.q.qq.com/api/json/qqa/CreateMiniCode?access_token=' . $access_token;

		$data = array(
			'appid' => JiangQie_API::option_value('qq_app_id'),
			'path' => 'pages/article/article?post_id=' . $post_id,
		);

		$args = array(
			'method'  => 'POST',
			'body' 	  => wp_json_encode($data),
			'headers' => array(
				'Content-Type' => 'application/json'
			),
			'cookies' => array()
		);

		$remote = wp_remote_post($api, $args);
		if (is_wp_error($remote)) {
			return $this->make_error('系统异常');
		}

		$content = wp_remote_retrieve_body($remote);
		if (strstr($content, 'errcode') !== false || strstr($content, 'errmsg') !== false) {
			return $this->make_success(plugins_url('images/qqacode.jpg', dirname(__FILE__)));
		}

		//输出二维码
		file_put_contents($qrcode, $content);

		//同步到媒体库
		$res = jiangqie_free_import_image2attachment($qrcode, $post_id, 'current', true);
		if (!is_wp_error($res)) {
			$qrcode_link = $uploads['baseurl'] . '/jiangqie_qqacode/' . $res;
		}

		return $this->make_success($qrcode_link);
	}

	/**
	 * 获取置顶的文章
	 */
	private function _getStickPosts($args)
	{
		$sticky_posts = get_option('sticky_posts');
		if (!$sticky_posts) {
			return [];
		}
		$args['posts_per_page'] = -1;
		$args['post__in'] = $sticky_posts;
		$args['ignore_sticky_posts'] = 1;
		$query = new WP_Query();
		$posts = $query->query($args);
		return $posts;
	}

}
