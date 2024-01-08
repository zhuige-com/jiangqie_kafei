/*
 * 酱茄小程序开源版
 * 作者: 追格
 * 文档：https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright © 2020-2024 www.zhuige.com All rights reserved.
 */

const Config = require("./config.js");

function makeURL(module, action) {
	return `https://${Config.JQ_DOMAIN}/wp-json/jiangqie/v1/${module}/${action}`;
}

module.exports = {
	/**
	 * 获取首页配置
	 */
	JIANGQIE_SETTING_HOME: makeURL('setting', 'home'),

	/**
	 * 获取热门配置
	 */
	JIANGQIE_SETTING_HOT: makeURL('setting', 'hot'),

	/**
	 * 获取分类配置
	 */
	JIANGQIE_SETTING_CATEGORY: makeURL('setting', 'category'),

	/**
	 * 获取我的配置
	 */
	JIANGQIE_SETTING_UCENTER: makeURL('setting', 'ucenter'),

	/**
	 * 获取登录配置
	 */
	JIANGQIE_SETTING_LOGIN: makeURL('setting', 'login'),
	
	/**
	 * 获取注销配置
	 */
	JIANGQIE_SETTING_LOGOUT: makeURL('setting', 'logout'),

	/**
	 * 获取分类 只获取一级分类
	 */
	JIANGQIE_CATEGORY_INDEX: makeURL('category', 'index'),

	/**
	 * 获取最新文章列表
	 */
	JIANGQIE_POSTS_LAST: makeURL('posts', 'last'),

	/**
	 * 获取某个分类下文章
	 */
	JIANGQIE_POSTS_CATEGORY: makeURL('posts', 'category'),

	/**
	 * 获取某个TAG下文章
	 */
	JIANGQIE_POSTS_TAG: makeURL('posts', 'tag'),

	/**
	 * 获取热门文章列表
	 */
	JIANGQIE_POSTS_HOT: makeURL('posts', 'hot'),

	/**
	 * 搜索文章
	 */
	JIANGQIE_POSTS_SEARCH: makeURL('posts', 'search'),

	/**
	 * 热门搜索
	 */
	JIANGQIE_POSTS_SEARCH_HOT: makeURL('posts', 'search/hot'),

	/**
	 * [我的]文章
	 */
	JIANGQIE_POSTS_MY: makeURL('posts', 'my'),

	/**
	 * 获取文章详情
	 */
	JIANGQIE_POST_DETAIL: makeURL('posts', 'detail'),

	/**
	 * 获取页面详情
	 */
	JIANGQIE_POST_PAGE: makeURL('posts', 'page'),

	/**
	 * 获取微信小程序码
	 */
	JIANGQIE_POST_WX_ACODE: makeURL('posts', 'wxacode'),

	/**
	 * 获取百度小程序码
	 */
	JIANGQIE_POST_BD_ACODE: makeURL('posts', 'bdacode'),

	/**
	 * 用户登录
	 */
	JIANGQIE_USER_LOGIN: makeURL('user', 'login3'),
	
	/**
	 * 用户注销
	 */
	JIANGQIE_USER_LOGOUT: makeURL('user', 'logout'),
	

	/**
	 * 用户登录 演示
	 */
	JIANGQIE_USER_LOGIN_TEST: makeURL('user', 'logintest'),

	/**
	 * 用户数据
	 */
	JIANGQIE_USER_INDEX: makeURL('user', 'index'),

	/**
	 * 用户 点赞文章
	 */
	JIANGQIE_USER_LIKE: makeURL('user', 'like'),

	/**
	 * 用户 收藏文章
	 */
	JIANGQIE_USER_FAVORITE: makeURL('user', 'favorite'),

	/**
	 * 用户 设置手机号
	 */
	JIANGQIE_USER_SET_MOBILE: makeURL('user', 'set_mobile'),

	/**
	 * 用户 设置昵称头像
	 */
	JIANGQIE_USER_SET_INFO: makeURL('user', 'set_info'),

	/**
	 * 文章评论
	 */
	JIANGQIE_COMMENT_INDEX: makeURL('comment', 'index'),

	/**
	 * 添加评论
	 */
	JIANGQIE_COMMENT_ADD: makeURL('comment', 'add'),

	/**
	 * 删除评论
	 */
	JIANGQIE_COMMENT_DELETE: makeURL('comment', 'delete'),

	/**
	 * 上传图片
	 */
	JIANGQIE_OTHER_UPLOAD: makeURL('other', 'upload'),
};