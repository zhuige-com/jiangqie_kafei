/*
 * 酱茄小程序开源版 v1.5.0
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/ky
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * Copyright © 2020-2021 www.jiangqie.com All rights reserved.
 */

function navigateBack() {
	uni.navigateBack({
		delta: 1,
		fail: function(res) {
			uni.switchTab({
				url: '/pages/index/index'
			});
		}
	});
}

function jiangqie() {
	let os = undefined;
	// #ifdef MP-WEIXIN
	os = 'weixin';
	uni.navigateToMiniProgram({
		appId: 'wxb0b82c1a4e82a353'
	})
	// #endif
	
	// #ifdef MP-BAIDU
	os = 'baidu';
	uni.navigateToMiniProgram({
		appId: 'UBGY8eyZqGooUziDVAz27P0KuMWj0eR1'
	})
	// #endif
	
	// #ifdef MP-QQ
	os = 'qq';
	uni.navigateToMiniProgram({
		appId: '1111804134'
	})
	// #endif
	
	if (!os) {
		uni.navigateTo({
			url: '/pages/webview/webview'
		})
	}
}

module.exports = {
	navigateBack,
	jiangqie
};
