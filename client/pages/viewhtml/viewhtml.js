/*
 * 酱茄小程序开源版 v1.1.0
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * License：MIT
 * Copyright ️ 2020 www.jiangqie.com All rights reserved.
 */

const Api = require('../../utils/api.js');
const Rest = require('../../utils/rest');
const WxParse = require('../../components/wxParse/wxParse');

Page({

    data: {

    },

    onLoad: function (options) {
        let that = this;
        Rest.get(Api.JIANGQIE_POST_PAGE, {
            page_id: options.page_id
        }).then(res => {
            wx.setNavigationBarTitle({
                title: res.data.title,
            })

            WxParse.wxParse('article', 'html', res.data.content, that, 5);
        });
    },

    onShareAppMessage: function () {
        return {
            title: getApp().app_name,
            path: 'pages/index/index',
        }
    },

})