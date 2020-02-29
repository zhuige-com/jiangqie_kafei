/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * License：MIT
 * Copyright ️ 2020 www.jiangqie.com All rights reserved.
 */

const Config = require('../../utils/config');
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
            title: getApp().app_name,
            path: 'pages/index/index',
        }
    },

    handlerCategoryClick: function (e) {
        let cat_id = e.currentTarget.dataset.id;
        let cat_name = e.currentTarget.dataset.name;
        wx.navigateTo({
            url: '/pages/list/list?cat_id=' + cat_id + '&title=' + cat_name
        })
    },

    handlerSearchClick: function(e) {
        wx.redirectTo({
          url: '/pages/search/search',
        })
    }
})