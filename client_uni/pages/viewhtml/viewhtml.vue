<template>
	<view>
		<view class="jiangqie-page-body">
			<mp-html :content="article"></mp-html>
		</view>

		<!--加载框 start-->
		<jiangqie-loading v-if="loading"></jiangqie-loading>
		<!--加载框 end-->
	</view>
</template>

<script>
	/*
	 * 酱茄小程序开源版 v1.4.0
	 * Author: 酱茄
	 * Help document: https://www.jiangqie.com/ky
	 * github: https://github.com/longwenjunjie/jiangqie_kafei
	 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
	 * Copyright © 2020-2021 www.jiangqie.com All rights reserved.
	 */
	const Api = require("@/utils/api.js");
	const Rest = require("@/utils/rest.js");

	import JiangqieLoading from "@/components/loading/loading";

	export default {
		data() {
			return {
				page_id: '',
				title: '',
				article: "",
				loading: false,
			};
		},

		components: {
			JiangqieLoading
		},
		
		props: {},
		
		onLoad: function(options) {
			let that = this;
			Rest.get(Api.JIANGQIE_POST_PAGE, {
				page_id: options.page_id
			}).then(res => {
				that.page_id = options.page_id;
				that.title = res.data.title;
				uni.setNavigationBarTitle({
					title: res.data.title
				});
				//WxParse.wxParse('article', 'html', res.data.content, that, 5)
				that.article = that.escape2Html(res.data.content);
			});
		},
		
		onShareAppMessage: function() {
			return {
				title: this.title,
				path: 'pages/viewhtml/viewhtml?page_id=' + this.page_id
			};
		},
		
		// #ifdef MP-WEIXIN
		onShareTimeline: function() {
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
<style>
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

</style>
