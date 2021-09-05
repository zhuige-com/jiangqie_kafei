/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/ky
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * Copyright © 2020-2021 www.jiangqie.com All rights reserved.
 */
const Auth = require("./auth.js");

/**
 * request封装
 */
function request(url, data = {}, method = "GET") {
	return new Promise(function(resolve, reject) {
		uni.showLoading();

		data.token = Auth.getToken();
		
		if (method == "GET") {
			data.t = new Date().getTime();
			data.r = Math.floor(Math.random() * 10000);
		}
		
		// #ifdef MP-WEIXIN
		data.os = 'wx';
		// #endif
		
		// #ifdef MP-BAIDU
		data.os = 'bd';
		// #endif
		
		// #ifdef MP-QQ
		data.os = 'qq';
		// #endif
		
		uni.request({
			url: url,
			data: data,
			method: method,
			success (res) {
				if (res.statusCode != 200) {
					reject(res.errMsg);
					return;
				}

				if (res.data.code == -1) {
					uni.navigateTo({
						url: '/pages/login/login'
					})
					return;
				}

				if (res.data.code == 0) {
					resolve(res.data);
					return;
				}
			},
			fail (err) {
				reject(err);
			},
			complete() {
				uni.hideLoading();
			}
		});
	});
}

/**
 * get请求
 */
function get(url, data = {}) {
	return request(url, data, 'GET');
}

/**
 * post请求
 */
function post(url, data = {}) {
	return request(url, data, 'POST');
}

module.exports = {
	get,
	post
};
