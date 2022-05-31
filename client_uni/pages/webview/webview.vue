<template>
	<view>
		<web-view :src="src"></web-view>
	</view>
</template>

<script>
	/*
	 * 酱茄小程序开源版
	 * 作者: 追格
	 * 文档：https://www.zhuige.com/docs/zxfree.html
	 * github: https://github.com/zhuige-com/jiangqie_kafei
	 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
	 * Copyright © 2020-2022 www.zhuige.com All rights reserved.
	 */

	export default {
		data() {
			return {
				src: 'https://www.zhuige.com/'
			};
		},

		onLoad(options) {
			if (options.src) {
				this.src = decodeURIComponent(options.src);
			}
			uni.setNavigationBarTitle({
				title: getApp().globalData.appName
			})
		},

		onShareAppMessage() {
			return {
				title: getApp().globalData.appName,
				path: 'pages/webview/webview?src=' + encodeURIComponent(this.src)
			};
		},

		onShareTimeline() {
			return {
				title: getApp().globalData.appName
			};
		},
	}
</script>

<style lang="scss" scoped>

</style>
