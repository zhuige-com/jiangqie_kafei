/*
 * 酱茄小程序开源版 v1.3.0
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/ky
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * License：MIT
 * Copyright © 2020-2021 www.jiangqie.com All rights reserved.
 */

const Constant = require('./constants');

//获取TOKEN
function getToken() {
    let user = wx.getStorageSync(Constant.JQ_USER_KEY);
    if (!user) {
        return false;
    }

    return user.token;
}

//注销
function logout() {
    wx.setStorageSync(Constant.JQ_USER_KEY, false);
}

module.exports = {
    //检查登录态
    checkSession: function () {
        wx.checkSession({
            fail() {
                logout();
            }
        })
    },

    logout: logout,

    //获取TOKEN
    getToken: getToken,

    //是否已登录
    isLogin: getToken,

    //获取用户信息
    getWXUser: function () {
        return new Promise(function (resolve, reject) {
            wx.login({
                success: function (res) {
                    resolve(res);
                },
                fail: function (err) {
                    reject(err);
                }
            });
        }).then(data => {
            return new Promise(function (resolve, reject) {
                wx.getUserInfo({
                    success: function (res) {
                        res.code = data.code;
                        resolve(res);
                    },
                    fail: function (err) {
                        reject(err);
                    }
                });
            });
        });
    },

    setUser: function(user) {
        wx.setStorageSync(Constant.JQ_USER_KEY, user);
    },

    getUser: function() {
        return wx.getStorageSync(Constant.JQ_USER_KEY);
    },
    
}