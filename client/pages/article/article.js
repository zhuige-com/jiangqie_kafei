/*
 * 酱茄小程序开源版 v1.5.0
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/ky
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright © 2020-2021 www.jiangqie.com All rights reserved.
 */

const Constants = require('../../utils/constants');
const Util = require('../../utils/util');
const Api = require('../../utils/api.js');
const Rest = require('../../utils/rest');
const Auth = require('../../utils/auth');
const WxParse = require('../../components/wxParse/wxParse');
const Poster = require('../../components/poster/poster/poster');

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

    //小程序码
    wxacode: '',

    //返回页面是否需要刷新
    needRefresh: true,

    onLoad: function (options) {
        if (options.scene) {
            this.post_id = decodeURIComponent(options.scene);
        } else if (options.post_id) {
            this.post_id = options.post_id;
        }

        //小程序码
        this.loadWxacode();
    },

    onShow: function () {
        if (!this.needRefresh) {
            this.needRefresh = true;
            return;
        }

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
            title: this.data.post.title,
            imageUrl: this.data.post.thumbnail,
            path: 'pages/article/article?post_id=' + this.post_id,
        }
    },

    onShareTimeline: function () {
        return {
            title: this.data.post.title,
            query: 'post_id=' + this.post_id,
            imageUrl: this.data.post.thumbnail,
        }
    },

    /**
     * 海报分享
     */
    sharePosterClick: function (e) {
        let posterConfig = {
            width: 750,
            height: 1334,
            backgroundColor: '#E6372F',
            debug: false,
            pixelRatio: 1,
            blocks: [{
                width: 690,
                height: 1000,
                x: 30,
                y: 234,
                backgroundColor: '#FFFFFF'
            }, ],
            texts: [{
                    x: 375,
                    y: 120,
                    baseLine: 'middle',
                    textAlign: 'center',
                    text: this.data.post.title,
                    width: 600,
                    fontSize: 38,
                    color: '#FFFFFF',
                },
                {
                    x: 70,
                    y: 780,
                    fontSize: 28,
                    lineHeight: 40,
                    baseLine: 'middle',
                    text: this.data.post.excerpt,
                    width: 600,
                    lineNum: 3,
                    color: '#000000',
                    zIndex: 200,
                },
                {
                    x: 360,
                    y: 1170,
                    baseLine: 'middle',
                    textAlign: 'center',
                    text: getApp().appName,
                    fontSize: 28,
                    color: '#888888',
                    zIndex: 200,
                }
            ],
            images: [
                {
                    width: 690,
                    height: 520,
                    x: 30,
                    y: 200,
                    url: this.data.post.thumbnail,
                    zIndex: 100
                },
                {
                    width: 200,
                    height: 200,
                    x: 275,
                    y: 920,
                    url: this.wxacode,
                }
            ]

        }

        this.setData({
            posterConfig: posterConfig
        }, () => {
            Poster.create(true); // 入参：true为抹掉重新生成 
        });
    },

    /**
     * 画报生成成功
     */
    onPosterSuccess(e) {
        this.needRefresh = false;

        const {
            detail
        } = e;
        wx.previewImage({
            current: detail,
            urls: [detail]
        })
    },

    /**
     * 画报生成失败
     */
    onPosterFail(err) {
        console.error(err);
    },

    /**
     * 文章中a标签点击
     */
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
    jumpBtn: function (options) {
        Util.navigateBack();
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
     * 加载小程序码
     */
    loadWxacode: function () {
        let that = this;
        Rest.get(Api.JIANGQIE_POST_WXACODE, {
            post_id: that.post_id
        }).then(res => {
            that.wxacode = res.data;
        }, err => {
            console.log(err);
        });
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
                pullUpOn: res.data.length >= Constants.JQ_PER_PAGE_COUNT,
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