<template>
	<view>
		<web-view :src="src"></web-view>
	</view>
</template>

<script>
	/*
	 * 酱茄小程序开源版 v1.5.0
	 * Author: 酱茄
	 * Help document: https://www.jiangqie.com/ky
	 * github: https://github.com/longwenjunjie/jiangqie_kafei
	 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
	 * Copyright © 2020-2021 www.jiangqie.com All rights reserved.
	 */
	
	export default {
		data() {
			return {
				src: 'https://www.jiangqie.com/'
			};
		},

		onLoad(options) {
			if (options.src) {
				this.src = options.src;
			}
			uni.setNavigationBarTitle({
				title: getApp().appName
			})
		},

		onShareAppMessage() {
			return {
				title: getApp().appName,
				path: 'pages/mix/webview/webview?src=' + this.src
			};
		},

		onShareTimeline() {
			return {
				title: getApp().appName
			};
		},
	}
</script>

<style lang="scss">

</style>
