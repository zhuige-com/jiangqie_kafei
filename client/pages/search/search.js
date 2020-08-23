/*
 * 酱茄小程序开源版 v1.1.0
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/ky
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * License：MIT
 * Copyright ️ 2020 www.jiangqie.com All rights reserved.
 */

const Constant = require('../../utils/constants');
const Util = require('../../utils/util');
const Api = require('../../utils/api.js');
const Rest = require('../../utils/rest');

Page({

    data: {
        canSearch: false,
        historySearch: [],
        hotSearch: [],
        placeholder: '',
    },

    keyword: '',

    onLoad: function (options) {
        let that = this;
        wx.getStorage({
            key: Constant.JQ_SEARCH_KEY,
            success(res) {
                that.setData({
                    historySearch: res.data
                });
            }
        });

        that.setData({
            placeholder: getApp().app_name
        });

        Rest.get(Api.JIANGQIE_POSTS_SEARCH_HOT).then(res => {
            that.setData({
                hotSearch: res.data
            });
        })
    },

    onShareAppMessage: function () {
        return {
            title: getApp().app_name,
            path: 'pages/index/index',
        }
    },

    onShareTimeline: function () {
        return {
            title: getApp().app_name,
        }
    },

    //输入
    handlerSearchInput: function (e) {
        this.keyword = e.detail.value;
        this.setData({
            canSearch: this.keyword.length > 0
        });
    },

    handerSearchConfirm: function(e) {
        this.search();
    },

    //搜索
    handerSearchClick: function (e) {
        this.search();
    },

    search: function() {
        let that = this;
        wx.getStorage({
            key: Constant.JQ_SEARCH_KEY,
            success(res) {
                let keys = [that.keyword];
                for (let i = 0; i < res.data.length && keys.length < Constant.JQ_SEARCH_MAX_COUNT; i++) {
                    if (that.keyword == res.data[i]) {
                        continue;
                    }

                    keys.push(res.data[i]);
                }

                that.setData({
                    historySearch: keys
                });

                wx.setStorage({
                    data: keys,
                    key: Constant.JQ_SEARCH_KEY,
                })
            },
            fail(e) {
                let keys = [that.keyword];

                that.setData({
                    historySearch: keys
                });

                wx.setStorage({
                    data: keys,
                    key: Constant.JQ_SEARCH_KEY,
                })
            }
        });

        wx.navigateTo({
            url: '/pages/list/list?search=' + this.keyword
        })
    },

    //取消搜索
    handerCancelClick: function (e) {
        Util.navigateBack();
    },

    //清楚搜索历史
    handlerClearHistory: function (e) {
        let that = this;

        wx.showModal({
            title: '提示',
            content: '确定要清除吗？',
            success(res) {
                if (res.confirm) {
                    wx.setStorage({
                        key: Constant.JQ_SEARCH_KEY,
                        data: [],
                        success() {
                            that.setData({
                                historySearch: []
                            });
                        }
                    });
                }
            }
        })
    },

    //点击 搜索历史
    handlerSearchItemClick: function (e) {
        let item = e.currentTarget.dataset.item;
        wx.navigateTo({
            url: '/pages/list/list?search=' + item
        })
    },

    //历史删除
    handlerSearchItemDelete: function (e) {
        let that = this;
        wx.showModal({
            title: '提示',
            content: '确定要删除吗？',
            success(res) {
                if (res.confirm) {
                    let item = e.currentTarget.dataset.item;
                    let keys = [];
                    for (let i = 0; i < that.data.historySearch.length; i++) {
                        if (item == that.data.historySearch[i]) {
                            continue;
                        }

                        keys.push(that.data.historySearch[i]);
                    }

                    that.setData({
                        historySearch: keys
                    });

                    wx.setStorage({
                        data: keys,
                        key: Constant.JQ_SEARCH_KEY,
                    })
                }
            }
        })
    }

})