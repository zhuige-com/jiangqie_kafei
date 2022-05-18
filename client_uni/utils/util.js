/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/ky
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright © 2020-2022 www.jiangqie.com All rights reserved.
 */

function navigateBack() {
	uni.navigateBack({
		delta: 1,
		fail: (res) => {
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

/**
 *  把html转义成HTML实体字符
 * @param str
 * @returns {string}
 * @constructor
 */
function htmlEncode(str) {
	var s = "";
	if (str.length === 0) {
		return "";
	}
	s = str.replace(/&/g, "&amp;");
	s = s.replace(/</g, "&lt;");
	s = s.replace(/>/g, "&gt;");
	s = s.replace(/ /g, "&nbsp;");
	s = s.replace(/\'/g, "&#39;"); //IE下不支持实体名称
	s = s.replace(/\"/g, "&quot;");
	return s;
}

/**
 *  转义字符还原成html字符
 * @param str
 * @returns {string}
 * @constructor
 */
function htmlRestore(str) {
	var s = "";
	if (str.length === 0) {
		return "";
	}
	s = str.replace(/&amp;/g, "&");
	s = s.replace(/&lt;/g, "<");
	s = s.replace(/&gt;/g, ">");
	s = s.replace(/&nbsp;/g, " ");
	s = s.replace(/&#39;/g, "\'");
	s = s.replace(/&quot;/g, "\"");
	return s;
}

function openLink(link) {
	if (!link) {
		return;
	}
	
	if (link.startsWith('/pages/')) {
		link = htmlRestore(link);
		uni.navigateTo({
			url: link,
			fail: () => {
				uni.redirectTo({
					url: link
				})
			}
		});
		return;
	}
	
	// #ifdef MP-WEIXIN
	if (link.startsWith('finder:')) {
		let finder = '';
		let feedId = '';
		let index = link.indexOf(';feedId:');
		if (index < 0) {
			finder = link.substring('finder:'.length);
		} else {
			finder = link.substring('finder:'.length, index);
			feedId = link.substring(index + ';feedId:'.length);
		}
		let params = {
			finderUserName: finder,
			fail: res => {
				uni.setClipboardData({
					data: link
				});
			}
		};
		
		if (feedId != '') {
			params.feedId = feedId;
			wx.openChannelsActivity(params);
		} else {
			wx.openChannelsUserProfile(params);
		}
		
		return;
	}
	// #endif
	
	uni.navigateToMiniProgram({
		appId: link,
		fail: res => {
			if (res.errMsg && res.errMsg.indexOf('fail cancel') < 0) {
				uni.showToast({
					icon: 'none',
					title: res.errMsg
				});
			}
		}
	});
}

module.exports = {
	navigateBack,
	jiangqie,
	openLink,
};
