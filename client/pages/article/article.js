/*
 * 酱茄小程序开源版 v1.0.8
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * License：MIT
 * Copyright ️ 2020 www.jiangqie.com All rights reserved.
 */

const Constants = require('../../utils/constants');
const Api = require('../../utils/api.js');
const Rest = require('../../utils/rest');
const Auth = require('../../utils/auth');
const WxParse = require('../../components/wxParse/wxParse');

Page({

    data: {
        post: {},
        post_like: 0,
        post_favorite: 0,
        comment_count: 0,
        comments: [],
        loadding: false,
        pullUpOn: true,
        loaded: false,

        show_comment_submit: false,
        comment_content: '',
        comment_count_change: 0
    },

    post_id: 0,
    comment_id: 0,

    onLoad: function (options) {
        this.post_id = options.post_id
    },

    onShow: function () {
        let that = this;
        Rest.get(Api.JIANGQIE_POST_DETAIL, {
            post_id: that.post_id
        }).then(res => {
            wx.setNavigationBarTitle({
                title: res.data.title,
            })

            that.setData({
                post: res.data,
                post_like: res.data.user.islike,
                post_favorite: res.data.user.isfavorite,
                comment_count: Number(res.data.comment_count),
                like_list: res.data.like_list,
            });

            WxParse.wxParse('article', 'html', res.data.content, that, 5);
        });

        this.loadComments(true);
    },

    onReachBottom: function () {
        if (!this.data.pullUpOn) {
            return;
        }

        this.loadComments(false);
    },

    onShareAppMessage: function () {
        return {
            title: that.data.post.title,
            imageUrl: that.data.post.thumbnail,
            path: 'pages/article/article?post_id=' + this.post_id,
        }
    },

    wxParseTagATap: function (e) {
        wx.setClipboardData({
            data: e.currentTarget.dataset.src
        });
    },

    /**
     * 点击 TAG
     */
    handlerTagClick: function (e) {
        let tag_id = e.currentTarget.dataset.id;
        let tag = e.currentTarget.dataset.tag;
        wx.navigateTo({
            url: '/pages/list/list?title=' + tag + '&tag_id=' + tag_id,
        })
    },

    /**
     * 跳转返回
     */
    //点击跳转到上级页面
    jumpBtn: function (options) {
        wx.navigateBack({
            delta: 1
        })
    },

    /**
     * 文章 点赞
     */
    handlerLikeClick: function (e) {
        let that = this;
        Rest.get(Api.JIANGQIE_USER_LIKE, {
            post_id: that.data.post.id
        }).then(res => {
            let avatar = Auth.getUser().avatar;
            var index = that.data.like_list.indexOf(avatar);
            if (index > -1) {
                that.data.like_list.splice(index, 1);
            } else {
                that.data.like_list.unshift(avatar);
            }

            that.setData({
                post_like: (that.data.post_like == 1 ? 0 : 1),
                like_list: that.data.like_list
            });
        })
    },

    /**
     * 评论 弹框
     */
    handlerCommentClick: function (e) {
        this.comment_id = 0;
        this.setData({
            show_comment_submit: true
        });
    },

    /**
     * 评论 取消
     */
    handlerCancelClick: function (e) {
        this.setData({
            show_comment_submit: false
        });
    },

    /**
     * 评论 提交
     */
    handlerCommentSubmit: function (e) {
        let that = this;
        Rest.get(Api.JIANGQIE_COMMENT_ADD, {
            post_id: that.post_id,
            parent_id: that.comment_id,
            content: that.data.comment_content
        }).then(res => {
            that.setData({
                comment_count_change: that.data.comment_count_change + (res.data.comment_verify == 1 ? 0 : 1),
                show_comment_submit: false
            });

            that.loadComments(true);
        });
    },

    /**
     * 评论 回复
     */
    handlerCommentReplyClick: function (e) {
        this.comment_id = e.currentTarget.dataset.id;
        this.setData({
            show_comment_submit: true
        });
    },

    /**
     * 评论 删除
     */
    handlerCommentDeleteClick: function (e) {
        let that = this;

        wx.showModal({
            title: '提示',
            content: '确定要删除吗？',
            success(res) {
                if (res.confirm) {
                    let comment_id = e.currentTarget.dataset.id;
                    Rest.get(Api.JIANGQIE_COMMENT_DELETE, {
                        comment_id: comment_id
                    }).then(res => {
                        that.setData({
                            comment_count_change: that.data.comment_count_change - 1
                        });
                        that.loadComments(true);
                    });
                }
            }
        });
    },

    /**
     * 评论输入
     */
    handlerContentInput: function (e) {
        this.setData({
            comment_content: e.detail.value
        });
    },

    /**
     * 文章 收藏
     */
    handlerFavoriteClick: function (e) {
        let that = this;
        Rest.get(Api.JIANGQIE_USER_FAVORITE, {
            post_id: that.data.post.id
        }).then(res => {
            that.setData({
                post_favorite: (that.data.post_favorite == 1 ? 0 : 1)
            });
        })
    },

    /**
     * 加载 评论
     */
    loadComments: function (refresh) {
        let that = this;

        that.setData({
            loadding: true
        });

        let offset = 0;
        if (!refresh) {
            offset = that.data.comments.length;
        }

        Rest.get(Api.JIANGQIE_COMMENT_INDEX, {
            post_id: that.post_id,
            offset: offset
        }).then(res => {
            that.setData({
                loaded: true,
                loadding: false,
                comments: refresh ? res.data : that.data.comments.concat(res.data),
                pullUpOn: res.data.length == Constants.JQ_PER_PAGE_COUNT,
            });
        });
    },

    handlerLoginCancelClick: function (e) {
        this.setData({
            showPopLogin: false
        });
    },

    handlerDoLoginClick: function (e) {
        wx.navigateTo({
            url: '/pages/login/login',
        });

        this.setData({
            showPopLogin: false
        });
    },
})