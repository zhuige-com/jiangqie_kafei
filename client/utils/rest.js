/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * License：MIT
 * Copyright ️ 2020 www.jiangqie.com All rights reserved.
 */

const Auth = require('./auth');
/**
 * request封装
 */
function request(url, data = {}, method = "GET") {
    return new Promise(function (resolve, reject) {
        wx.showLoading();

        data.token = Auth.getToken();

        wx.request({
            url: url,
            data: data,
            method: method,
            success: function (res) {
                wx.hideLoading();

                if (res.statusCode == 200) {
                    if (res.data.code == 0 || res.data.code == -1) {
                        resolve(res.data);
                    } else {
                        reject(res.data.msg);
                    }
                } else {
                    reject(res.errMsg);
                }
            },
            fail: function (err) {
                wx.hideLoading();
                reject(err);
            }
        })
    });
}

/**
 * get请求
 */
function get(url, data = {}) {
    return request(url, data, 'GET')
}

/**
 * post请求
 */
function post(url, data = {}) {
    return request(url, data, 'POST')
}

module.exports = {
    get,
    post,
}