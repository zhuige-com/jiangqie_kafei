/*
 * 酱茄小程序开源版
 * 作者: 追格
 * 文档：https://www.zhuige.com/docs/zxfree.html
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright © 2020-2024 www.zhuige.com All rights reserved.
 */

function clickHref(data) {
	// data 中包含 href、innerText、rel、target 以及其他自定义属性
	// 如果需要兼容 pc web、h5、小程序多平台，建议充分利用【自定义属性】
	// 比如链接：<a href="https://www.zhuige.com/" xcx-link="/pages/about/about">追格</a>
	// 网页上点击链接打开网站，小程序里则可以使用 uni.navigateTo 打开 /pages/about/about 页面
	// 示例代码：uni.navigateTo({url: data['xcx-link']});

	// console.log(data);

	// 1.复制链接
	uni.setClipboardData({
		data: data.href,
		success: () => {
			uni.showToast({
				icon: 'none',
				title: '链接已复制，请在浏览器中打开'
			})
		},
		fail: (res) => {
			if (res.errMsg && res.errMsg.indexOf('cancel') < 0) {
				uni.showToast({
					icon: 'none',
					title: res.errMsg
				})
			}
		}
	})

	// // 2.直接打开 - 只适用于业务域名下链接
	// uni.navigateTo({
	// 	url: '/pages/webview/webview?src=' + encodeURIComponent(data.href),
	// 	fail(res) {
	// 		uni.redirectTo({
	// 			url: '/pages/webview/webview?src=' + encodeURIComponent(data.href)
	// 		});
	// 	}
	// });

	// // 3.假设存在属性 xcx-link，且值为一个合法的小程序页面地址
	// // xcx-link 是 a 的自定义属性，需要在发布文章时设置好
	// uni.navigateTo({
	// 	url: data['xcx-link']
	// });
}

module.exports = {
	clickHref,
};