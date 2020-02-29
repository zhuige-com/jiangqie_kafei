/*
 * 酱茄小程序开源版
 * Author: 	酱茄
 * Help document：https://www.jiangqie.com/docs/kaiyuan/id1
 * License：MIT
 * Copyright ️ 2020 www.jiangqie.com All rights reserved.
 */

const Config = require('../../utils/config');

Page({

    data: {

    },

    onLoad: function (options) {

    },

    onShareAppMessage: function () {
        return {
            title: getApp.app_,
            path: 'pages/index/index',
        }
    },
    
})