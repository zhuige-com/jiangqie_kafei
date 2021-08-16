/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/ky
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * Copyright © 2020-2021 www.jiangqie.com All rights reserved.
 */

const Constant = require("./constants.js"); //获取TOKEN

function getToken() {
	let user = uni.getStorageSync(Constant.JQ_USER_KEY);

	if (!user) {
		return false;
	}

	return user.token;
} //注销


function logout() {
	uni.setStorageSync(Constant.JQ_USER_KEY, false);
}

module.exports = {
	//检查登录态
	checkSession: function() {
		uni.checkSession({
			fail() {
				logout();
			}
		});
	},

	logout: logout,

	//获取TOKEN
	getToken: getToken,

	//是否已登录
	isLogin: getToken,

	//获取用户信息
	getWXUser: function() {
		return new Promise(function(resolve, reject) {
			uni.login({
				success: function(res) {
					resolve(res);
				},
				fail: function(err) {
					reject(err);
				}
			});
		});
	},

	setUser: function(user) {
		uni.setStorageSync(Constant.JQ_USER_KEY, user);
	},

	getUser: function() {
		return uni.getStorageSync(Constant.JQ_USER_KEY);
	}
};
