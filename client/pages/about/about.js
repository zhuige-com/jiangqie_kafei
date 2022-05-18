/*
 * 酱茄小程序开源版 v1.5.0
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/ky
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright © 2020-2021 www.jiangqie.com All rights reserved.
 */

const Constant = require('../../utils/constants');

Page({
    data: {
        version: Constant.JQ_VERSION
    },

    onLoad: function (options) {

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

    copy: function (e) {
        let text = e.currentTarget.dataset.text;
        wx.setClipboardData({
            data: text,
            success(res) {
                wx.getClipboardData({
                    success(res) {
                        wx.showToast({
                            title: '网址已复制',
                        });
                    }
                })
            }
        })
    },

    tipClick: function () {
        wx.showToast({
            icon: 'none',
            title: '酱茄小程序开源版 v1.5.0',
        })
    }
})