<template>
	<view>
		<web-view :src="src"></web-view>
	</view>
</template>

<script>
	/*
	 * 酱茄小程序开源版
	 * Author: 酱茄
	 * Help document: https://www.jiangqie.com/ky
	 * github: https://github.com/longwenjunjie/jiangqie_kafei
	 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
	 * Copyright © 2020-2022 www.jiangqie.com All rights reserved.
	 */

	export default {
		data() {
			return {
				src: 'https://www.jiangqie.com/'
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
