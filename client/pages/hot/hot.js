/*
 * 酱茄小程序开源版 v1.4.0
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/ky
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * License：MIT
 * Copyright © 2020-2021 www.jiangqie.com All rights reserved.
 */

const Constants = require('../../utils/constants');
const Api = require('../../utils/api.js');
const Rest = require('../../utils/rest');

Page({

    data: {
        setting: {},
        tabbar: ['浏览数', '点赞数', '收藏数', '评论数'],
        currentTab: 0,
        posts: [],
        loadding: false,
        pullUpOn: true,
        loaded: false,

        tl_background: Api.JIANGQIE_BG_TIMELINE,
    },

    default: {
        background: Api.JIANGQIE_BG_HOT,
        title: '热门标题，请在后台修改',
        description: '热门描述，请在后台修改',
    },

    sorts: ['views', 'likes', 'favorites', 'comments'],

    onLoad: function (options) {
        //获取配置
        let that = this;
        Rest.get(Api.JIANGQIE_SETTING_HOT).then(res => {
            that.setData({
                setting: {
                    background: res.data.background ? res.data.background : that.default.background,
                    title: res.data.title ? res.data.title : that.default.title,
                    description: res.data.description ? res.data.description : that.default.description,
                }
            });
        })

        this.loadPosts(true);
    },

    onReachBottom: function () {
        if (!this.data.pullUpOn) {
            return;
        }

        this.loadPosts(false);
    },

    onShareAppMessage: function () {
        return {
            title: getApp().appName,
            path: 'pages/index/index',
        }
    },

    onShareTimeline: function () {
        return {
            title: getApp().appName,
        }
    },

    // 点击标题切换当前页时改变样式
    swichNav: function (e) {
        let cur = e.currentTarget.dataset.current;
        if (this.data.currentTab == cur) {
            return false;
        }

        this.setData({
            currentTab: cur
        })

        this.data.posts = [];
        this.loadPosts(true);
    },

    handlerHotArticle: function (e) {
        let post_id = e.currentTarget.dataset.id;
        wx.navigateTo({
            url: '/pages/article/article?post_id=' + post_id
        })
    },

    //加载数据
    loadPosts(refresh) {
        let that = this;

        that.setData({
            loadding: true
        });

        Rest.get(Api.JIANGQIE_POSTS_HOT, {
            'offset': refresh ? 0 : that.data.posts.length,
            'sort': that.sorts[that.data.currentTab]
        }).then(res => {
            that.setData({
                loaded: true,
                loadding: false,
                posts: refresh ? res.data : that.data.posts.concat(res.data),
                pullUpOn: res.data.length >= Constants.JQ_PER_PAGE_COUNT
            });

        })
    }
})