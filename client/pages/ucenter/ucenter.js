/*
 * 酱茄小程序开源版 v1.2.2
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/ky
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * License：MIT
 * Copyright © 2020-2021 www.jiangqie.com All rights reserved.
 */

const Auth = require('../../utils/auth');
const Api = require('../../utils/api.js');
const Rest = require('../../utils/rest');

Page({

    data: {
        setting: {},
        user: undefined,
        menu: {},
    },

    default: {
        background: Api.JIANGQIE_BG_MY,
        menu: [ //views,likes,favorites,comments,about,feedback,contact,clear,split,link,page
            {
                tp: 'views',
                icon: '../../images/icon_view.png',
                title: '我的浏览',
                line: 1,
            },
            {
                tp: 'likes',
                icon: '../../images/icon_like.png',
                title: '我的点赞',
                line: 1,
            },
            {
                tp: 'favorites',
                icon: '../../images/icon_fav.png',
                title: '我的收藏',
                line: 1,
            },
            {
                tp: 'comments',
                icon: '../../images/icon_com.png',
                title: '我的评论',
                line: 0,
            },
            {
                tp: 'split'
            },
            {
                tp: 'about',
                icon: '',
                title: '关于我们',
                line: 1,
            },
            {
                tp: 'feedback',
                icon: '',
                title: '意见反馈',
                line: 1,
            },
            {
                tp: 'contact',
                icon: '',
                title: '在线客服',
                line: 1,
            },
            {
                tp: 'clear',
                icon: '',
                title: '清除缓存',
                line: 0,
            },
        ]
    },

    onShow: function (options) {
        let that = this;
        let user = Auth.getUser();
        that.setData({
            user: user
        });

        Rest.get(Api.JIANGQIE_SETTING_UCENTER).then(res => {
            let menu = that.default.menu;
            if (res.data.menu.length > 0) {
                menu = res.data.menu;
            }
            that.setData({
                setting: {
                    background: res.data.background ? res.data.background : that.default.background,
                },
                menu: menu
            });
        })
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

    handlerPostTrack: function (e) {
        if (!Auth.getUser()) {
            this.setData({
                showPopLogin: true
            });
            return;
        }

        let track = e.currentTarget.dataset.track;
        wx.navigateTo({
            url: '/pages/list/list?track=' + track
        })
    },

    handlerAbout: function (e) {
        wx.navigateTo({
            url: '/pages/about/about',
        })
    },

    handlerClearCache: function (e) {
        wx.showModal({
            title: '提示',
            content: '清除缓存 需要重新登录',
            success(res) {
                if (res.confirm) {
                    wx.clearStorageSync();
                    wx.showToast({
                        title: '清除完毕',
                    });
                    wx.reLaunch({
                        url: '/pages/index/index',
                    })
                }
            }
        });
    },

    handlerLinkClick: function (e) {
        let link = e.currentTarget.dataset.link;
        if (link.startsWith('/pages')) {
            wx.navigateTo({
                url: link,
            })
        } else {
            wx.navigateToMiniProgram({
                appId: link,
                fail: res => {
                    wx.showToast({
                        title: '无效链接',
                    })
                }
            })
        }
    },

    handlerPageClick: function (e) {
        let page_id = e.currentTarget.dataset.page_id;
        wx.navigateTo({
            url: '/pages/viewhtml/viewhtml?page_id=' + page_id,
        })
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