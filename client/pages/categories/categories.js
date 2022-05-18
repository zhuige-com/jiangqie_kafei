/*
 * 酱茄小程序开源版 v1.5.0
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/ky
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright © 2020-2021 www.jiangqie.com All rights reserved.
 */

const Api = require('../../utils/api');
const Rest = require('../../utils/rest');

Page({
    data: {
        setting: {},
        categories: [],
        categorie_cover: Api.JIANGQIE_CAT_COVER
    },

    default: {
        background: Api.JIANGQIE_BG_CATEGORY,
        title: '分类标题，请在后台修改',
        description: '分类描述，请在后台修改',
    },

    onLoad: function (options) {
        //获取配置
        let that = this;
        Rest.get(Api.JIANGQIE_SETTING_CATEGORY).then(res => {
            that.setData({
                setting: {
                    background: res.data.background ? res.data.background : that.default.background,
                    title: res.data.title ? res.data.title : that.default.title,
                    description: res.data.description ? res.data.description : that.default.description,
                }
            });
        });

        //获取一级分类
        Rest.get(Api.JIANGQIE_CATEGORY_INDEX).then(res => {
            that.setData({
                categories: res.data
            });
        });
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

    handlerCategoryClick: function (e) {
        let cat_id = e.currentTarget.dataset.id;
        let cat_name = e.currentTarget.dataset.name;
        wx.navigateTo({
            url: '/pages/list/list?cat_id=' + cat_id + '&title=' + cat_name
        })
    },

})