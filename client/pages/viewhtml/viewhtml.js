/*
 * 酱茄小程序开源版 v1.5.0
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/ky
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright © 2020-2021 www.jiangqie.com All rights reserved.
 */

const Api = require('../../utils/api.js');
const Rest = require('../../utils/rest');
const WxParse = require('../../components/wxParse/wxParse');

Page({

    data: {

    },

    page_id: '',
    title: '',

    onLoad: function (options) {
        let that = this;
        Rest.get(Api.JIANGQIE_POST_PAGE, {
            page_id: options.page_id
        }).then(res => {
            that.page_id = options.page_id;
            that.title = res.data.title;

            wx.setNavigationBarTitle({
                title: res.data.title,
            })

            WxParse.wxParse('article', 'html', res.data.content, that, 5);
        });
    },

    onShareAppMessage: function () {
        return {
            title: this.title,
            path: 'pages/viewhtml/viewhtml?page_id=' + this.page_id,
        }
    },

    onShareTimeline: function () {
        return {
            title: this.title,
            query: 'page_id=' + this.page_id
        }
    },

})