<template>
	<view class="jiangqie-page-body">
		<mp-html :content="article"></mp-html>
	</view>
</template>

<script>
	/*
	 * 酱茄小程序开源版
	 * 作者: 追格
	 * 文档：https://www.zhuige.com/docs/zxfree.html
	 * github: https://github.com/zhuige-com/jiangqie_kafei
	 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
	 * Copyright © 2020-2024 www.zhuige.com All rights reserved.
	 */

	const Api = require("@/utils/api.js");
	const Rest = require("@/utils/rest.js");

	export default {
		components: {
			
		},
		
		data() {
			this.page_id = '';
			this.title = '';
			return {
				article: "",
			};
		},

		onLoad(options) {
			Rest.get(Api.JIANGQIE_POST_PAGE, {
				page_id: options.page_id
			}).then(res => {
				this.page_id = options.page_id;
				this.title = res.data.title;
				uni.setNavigationBarTitle({
					title: res.data.title
				});

				this.article = res.data.content;
			});
		},

		onShareAppMessage() {
			return {
				title: this.title,
				path: 'pages/viewhtml/viewhtml?page_id=' + this.page_id
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: this.title,
				query: 'page_id=' + this.page_id
			};
		},
		// #endif

		methods: {

		}
	};
</script>

<style lang="scss" scoped>
	.jiangqie-page-body {
		padding: 0 30rpx 30rpx 30rpx;
	}

	.wxParse-p {
		margin-top: 30rpx;
		font-size: 30rpx;
		color: #333333;
		line-height: 40rpx;
	}

	.wxParse-inline {
		line-height: 70rpx;
	}

	.jiangqie-page-body,
	.jiangqie-page-body rich-text,
	.jiangqie-page-body uni-view {
		line-height: 2.2rem;
	}
</style>