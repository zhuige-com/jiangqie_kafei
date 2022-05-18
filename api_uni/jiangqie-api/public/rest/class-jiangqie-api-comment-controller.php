<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright ️© 2020-2022 www.jiangqie.com All rights reserved.
 */

class JiangQie_API_Comment_Controller extends JiangQie_API_Base_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->module = 'comment';
	}

	public function register_routes()
	{
		//文章的评论列表
		register_rest_route($this->namespace, '/' . $this->module . '/index', [
			[
				'callback' => [$this, 'comment_index'],
				'args' => array_merge($this->params_paging(), [
					'token' => [
						'default' => '',
						'description' => '用户token',
						'type' => 'string',
					],
					'post_id' => [
						'default' => 0,
						'description' => '文章ID',
						'type' => 'integer',
					],
				])
			]
		]);

		//发布评论
		register_rest_route($this->namespace, '/' . $this->module . '/add', [
			[
				'callback' => [$this, 'comment_add'],
				'args' => [
					'token' => [
						'default' => '',
						'description' => '用户token',
						'type' => 'string',
					],
					'post_id' => [
						'default' => 0,
						'description' => '文章ID',
						'type' => 'integer',
					],
					'parent_id' => [
						'default' => 0,
						'description' => '评论ID',
						'type' => 'integer',
					],
					'content' => [
						'default' => '',
						'description' => '评论内容',
						'type' => 'string',
					],
				]
			]
		]);

		//删除评论
		register_rest_route($this->namespace, '/' . $this->module . '/delete', [
			[
				'callback' => [$this, 'comment_delete'],
				'args' => [
					'token' => [
						'default' => '',
						'description' => '用户token',
						'type' => 'string',
					],
					'comment_id' => [
						'default' => 0,
						'description' => '评论ID',
						'type' => 'integer',
					],
				]
			]
		]);
	}

	/**
	 * 文章的评论列表
	 */
	public function comment_index($request)
	{
		$post_id = (int)($this->param_value($request, 'post_id', 0));
		if (empty($post_id)) {
			return $this->make_error('缺少参数');
		}

		$offset = (int)($this->param_value($request, 'offset', 0));

		$my_user_id = $this->check_login($request);

		$comments = $this->get_comments($post_id, $my_user_id, 0, $offset);

		foreach ($comments as &$comment) {
			$comment['replys'] = $this->get_comments($post_id, $my_user_id, $comment['id']);
		}

		return $this->make_success($comments);
	}

	/**
	 * 发布评论
	 */
	public function comment_add($request)
	{
		$user_id = $this->check_login($request);
		if (!$user_id) {
			return $this->make_error('还没有登陆', -1);
		}

		if (!JiangQie_API::option_value('switch_comment')) {
			return $this->make_error('评论功能未开启');
		}

		$post_id = (int)($this->param_value($request, 'post_id', 0));
		$parent_id = (int)($this->param_value($request, 'parent_id', 0));
		$content = $this->param_value($request, 'content', '');
		if (empty($post_id) || empty($content)) {
			return $this->make_error('缺少参数');
		}

		if(!$this->msg_sec_check($content)) {
			return $this->make_error('请勿发布敏感信息');
		}

		$comment_approved = JiangQie_API::option_value('switch_comment_verify') ? 0 : 1;
		$comment_id = wp_insert_comment([
			'comment_post_ID' => $post_id,
			'comment_content' => $content,
			'comment_parent' => $parent_id,
			'comment_approved' => $comment_approved,
			'user_id' => $user_id,
		]);

		return $this->make_success(['add_count' => $comment_approved]);
	}

	/**
	 * 删除评论
	 */
	public function comment_delete($request)
	{
		$user_id = $this->check_login($request);
		if (!$user_id) {
			return $this->make_error('还没有登陆', -1);
		}

		$comment_id = (int)($this->param_value($request, 'comment_id', 0));
		if (empty($comment_id)) {
			return $this->make_error('缺少参数');
		}

		global $wpdb;
		$table_comments = $wpdb->prefix . 'comments';
		$wpdb->query($wpdb->prepare("DELETE FROM `$table_comments` WHERE `comment_ID`=%d OR `comment_parent`=%d", $comment_id, $comment_id));

		return $this->make_success();
	}

	/**
	 * 评论内容
	 */
	private function get_comments($post_id, $my_user_id, $parent, $offset = null)
	{
		global $wpdb;
		$per_page_count = JiangQie_API::POSTS_PER_PAGE;
		$table_comments = $wpdb->prefix . 'comments';
		$fields = 'comment_ID, comment_author, comment_date, comment_content, comment_approved, user_id';
		$where = $wpdb->prepare("comment_post_ID=%d AND comment_parent=%d", $post_id, $parent);
		if ($my_user_id) {
			$where = $where . $wpdb->prepare(" AND (comment_approved=1 OR user_id=%d)", $my_user_id);
		} else {
			$where = $where . " AND comment_approved=1";
		}

		$limit = '';
		if ($offset !== null) {
			$limit = $wpdb->prepare("LIMIT %d, %d", $offset, $per_page_count);
		}

		$result = $wpdb->get_results(
			"SELECT $fields FROM `$table_comments` WHERE $where ORDER BY comment_ID DESC $limit"
		);
		$comments = [];
		foreach ($result as $comment) {
			$name = get_user_meta($comment->user_id, 'nickname', true);
			if (!$name) {
				$name = $comment->comment_author;
			}

			$avatar = get_user_meta($comment->user_id, 'jiangqie_avatar', true);

			$comments[] = [
				'id' => $comment->comment_ID,
				'user' => [
					'id' => $comment->user_id,
					'name' => $name,
					'avatar' => $avatar,
					'is_me' => ($comment->user_id === $my_user_id) ? 1 : 0,
				],
				'content' => $comment->comment_content,
				'approved' => $comment->comment_approved,
				'time' => $this->time_beautify($comment->comment_date),
			];
		}

		return $comments;
	}
}
