<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * Copyright ️ 2020 www.jiangqie.com All rights reserved.
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
				'args' => $this->params_paging()
			]
		]);

		//获取某个分类下的文章
		register_rest_route($this->namespace, '/' . $this->module . '/category', [
			[
				'callback' => [$this, 'get_category_posts'],
				'args' => $this->params_paging()
			]
		]);

		//获取某个TAG下的文章
		register_rest_route($this->namespace, '/' . $this->module . '/tag', [
			[
				'callback' => [$this, 'get_tag_posts'],
				'args' => $this->params_paging()
			]
		]);

		//搜索
		register_rest_route($this->namespace, '/' . $this->module . '/search', [
			[
				'callback' => [$this, 'get_search_posts'],
				'args' => $this->params_paging()
			]
		]);

		//热门搜索
		register_rest_route($this->namespace, '/' . $this->module . '/search/hot', [
			[
				'callback' => [$this, 'get_search_hot'],
				'args' => $this->params_paging()
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
				]
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
				]
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
				])
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
				])
			]
		]);
	}

	/**
	 * 按【时间倒序】获取文章列表
	 */
	public function get_last_posts($request)
	{
		$offset = $this->param_value($request, 'offset', 0);

		$args = [
			'posts_per_page' => JiangQie_API::POSTS_PER_PAGE,
			'offset' => $offset,
			'orderby' => 'date',
		];

		$hide_cat = JiangQie_API::option_value('hide_cat');
		if (!empty($hide_cat)) {
			$args['category__not_in'] = explode(',', $hide_cat);
		}

		$posts = $this->get_posts($args);
		return $this->make_success($posts);
	}

	/**
	 * 搜索文章
	 */
	public function get_search_posts($request)
	{
		$offset = $this->param_value($request, 'offset', 0);
		$search = $this->param_value($request, 'search', '');

		if (empty($search)) {
			return $this->make_error('缺少参数');
		}

		global $wpdb;
		$table_post_search = $wpdb->prefix . 'jiangqie_post_search';
		$times = $wpdb->get_var($wpdb->prepare("SELECT times FROM `$table_post_search` WHERE search=%s", $search));
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
			$args['category__not_in'] = explode(',', $hide_cat);
		}

		$posts = $this->get_posts($args);
		return $this->make_success($posts);
	}

	/**
	 * 热门搜索
	 */
	public function get_search_hot($request)
	{
		global $wpdb;
		$table_post_search = $wpdb->prefix . 'jiangqie_post_search';
		$result = $wpdb->get_results($wpdb->prepare("SELECT search FROM `$table_post_search` ORDER BY times DESC LIMIT 0, 10"));
		$searchs = array_column($result, 'search');

		return $this->make_success($searchs);
	}
	
	/**
	 * 获取文章详情
	 */
	public function get_post_detail($request)
	{
		$post_id = $this->param_value($request, 'post_id');
		if (!$post_id) {
			return $this->make_error('缺少参数');
		}

		$args = [
			'p' => $post_id
		];

		$query = new WP_Query();
		$result = $query->query($args);
		$post = apply_filters('jiangqie_post_for_detail', $result);

		//查询tag
		$tags = get_the_tags($post_id);
		if (!$tags) {
			$post['tags'] = [];
		} else {
			$post['tags'] = apply_filters('jiangqie_tag_for_detail', $tags);
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
		$users = $wpdb->get_results("SELECT user_id FROM `$table_post_like` WHERE post_id=$post_id ORDER BY id DESC");
		$post['like_list'] = [];
		if (!empty($users)) {
			foreach ($users as $user) {
				$post['like_list'][] = get_user_meta($user->user_id, 'jiangqie_avatar', true);
			}
		}

		//收藏数
		$post['favorites'] = (int) get_post_meta($post_id, "favorites", true);

		//能否评论
		$post['switch_comment'] = JiangQie_API::option_value('switch_comment')==='yes'?1:0;

		//用户数据
		$user = [];
		$user_id = $this->check_login($request);
		if ($user_id) {
			$table_post_like = $wpdb->prefix . 'jiangqie_post_like';
			$post_like_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM `$table_post_like` WHERE user_id=%d AND post_id=%d", $user_id, $post_id));
			$user['islike'] = $post_like_id ? 1 : 0;

			$table_post_favorite = $wpdb->prefix . 'jiangqie_post_favorite';
			$post_favorite_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM `$table_post_favorite` WHERE user_id=%d AND post_id=%d", $user_id, $post_id));
			$user['isfavorite'] = $post_favorite_id ? 1 : 0;

			//添加文章浏览记录
			$table_post_view = $wpdb->prefix . 'jiangqie_post_view';
			$post_view_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM `$table_post_view` WHERE user_id=%d AND post_id=%d", $user_id, $post_id));
			if (!$post_view_id) {
				$wpdb->insert($table_post_view, [
					'user_id' => $user_id,
					'post_id' => $post_id,
				]);
			}
		}
		$post['user'] = $user;

		return $this->make_success($post);
	}

	/**
	 * 获取页面详情
	 */
	public function get_post_page($request)
	{
		$page_id = $this->param_value($request, 'page_id');
		if (!$page_id) {
			return $this->make_error('缺少参数');
		}

		$args = [
			'p' => $page_id
		];

		global $wpdb;
		$table_post = $wpdb->prefix . 'posts';
		$result = $wpdb->get_row("SELECT post_title, post_content FROM `$table_post` WHERE ID=$page_id");
		$page['title'] = $result->post_title;
		$page['content'] = apply_filters('the_content', $result->post_content);

		return $this->make_success($page);
	}

	//获取某一TAG下的文章
	public function get_tag_posts($request)
	{
		$offset = $this->param_value($request, 'offset', 0);
		$tag_id = $this->param_value($request, 'tag_id', 0);

		$args = [
			'posts_per_page' => JiangQie_API::POSTS_PER_PAGE,
			'offset' => $offset,
			'orderby' => 'date',
			'tag_id' => $tag_id
		];

		$posts = $this->get_posts($args);
		return $this->make_success($posts);
	}

	//获取某一分类下的文章
	public function get_category_posts($request)
	{
		$offset = $this->param_value($request, 'offset', 0);
		$cat_id = $this->param_value($request, 'cat_id', 0);

		$args = [
			'posts_per_page' => JiangQie_API::POSTS_PER_PAGE,
			'offset' => $offset,
			'orderby' => 'date',
			'cat' => $cat_id
		];

		$posts = $this->get_posts($args);
		return $this->make_success($posts);
	}

	//热门 浏览数[views] 点赞数[likes] 评论数[commnets]
	public function get_hot_posts($request)
	{
		$offset = $this->param_value($request, 'offset', 0);

		$args = [
			'posts_per_page' => JiangQie_API::POSTS_PER_PAGE,
			'offset' => $offset,
			'order' => 'DESC',
		];

		$hide_cat = JiangQie_API::option_value('hide_cat');
		if (!empty($hide_cat)) {
			$args['category__not_in'] = explode(',', $hide_cat);
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

			// $post['views'] = (int)get_post_meta($post['id'], 'views', true);
			// $post['likes'] = (int)get_post_meta($post['id'], 'likes', true);
			// $post['favorites'] = (int)get_post_meta($post['id'], 'favorites', true);
			if ($sort == 'views' || $sort == 'likes' || $sort == 'favorites') {
				$post[$sort] = (int)get_post_meta($post['id'], $sort, true);
			}

			//美化时间
			$post['time'] = $this->time_beautify($post['time']);
		}
		return $this->make_success($posts);
	}

	//我的文章 浏览数[views] 点赞数[likes] 评论数[commnets] 收藏[favorite]
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

		$offset = $this->param_value($request, 'offset', 0);

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
		$post_ids = $wpdb->get_results("SELECT distinct $field FROM `$table_name` WHERE user_id=$user_id ORDER BY $orderby DESC LIMIT $offset, $per_page_count");
		if (empty($post_ids)) {
			return $this->make_success([]);
		}

		$args = [
			'post__in' => array_column($post_ids, $field),
			'posts_per_page' => $per_page_count,
			'orderby' => 'post__in'
		];

		$posts = $this->get_posts($args);
		return $this->make_success($posts);
	}

	/**
	 * 处理文章
	 */
	private function get_posts($args)
	{
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

			//美化时间
			$post['time'] = $this->time_beautify($post['time']);

			$post['views'] = (int)get_post_meta($post['id'], 'views', true);
		}

		return $posts;
	}

}
