<template>
	<view class="container">
		<view class="categorie-header">
			<view v-if="setting.title || setting.description" class="categorie-text">
				<text>{{setting.title}}</text>
				<view>{{setting.description}}</view>
			</view>
			<image v-if="setting.background && setting.background.length>0" :src="setting.background" mode="aspectFill">
			</image>
		</view>

		<!-- #ifdef MP-WEIXIN -->
		<template v-if="wx_ad">
			<ad :unit-id="wx_ad"></ad>
		</template>
		<!-- #endif -->

		<view class="right-box">

			<view v-for="(item, index) in categories" :key="index" class="sortbox" :data-id="item.id"
				:data-name="item.name" @tap.stop="handlerCategoryClick">
				<image :src="item.cover" mode="aspectFill" class="sortimage"></image>
				<view class="sorttitle">{{item.name}}</view>
				<view class="sortsummary">{{item.description}}</view>
			</view>

		</view>

		<view class="jiangqie-nomore">
			<view class="jiangqie-text">© 追格 zhuige.com</view>
		</view>
	</view>
</template>

<script>
	/*
	 * 酱茄小程序开源版
	 * 作者: 追格
	 * 文档：https://www.zhuige.com/docs/zxfree.html
	 * github: https://github.com/zhuige-com/jiangqie_kafei
	 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
	 * Copyright © 2020-2023 www.zhuige.com All rights reserved.
	 */
	const Api = require("@/utils/api.js");
	const Rest = require("@/utils/rest.js");

	export default {
		data() {
			this.default = {
				title: '分类标题，请在后台修改',
				description: '分类描述，请在后台修改'
			};
			
			return {
				setting: {
					title: "",
					description: "",
					background: ""
				},
				wx_ad: undefined,
				categories: []
			};
		},

		onLoad(options) {
			//获取配置
			Rest.get(Api.JIANGQIE_SETTING_CATEGORY).then(res => {
				this.setting = {
					background: res.data.background,
					title: res.data.title ? res.data.title : this.default.title,
					description: res.data.description ? res.data.description : this.default.description
				};
				this.wx_ad = res.data.wx_ad;

				// #ifdef MP-BAIDU
				swan.setPageInfo({
					title: this.setting.title,
					description: this.setting.description,
					keywords: '文章分类',
				});
				// #endif
			});

			//获取一级分类
			Rest.get(Api.JIANGQIE_CATEGORY_INDEX).then(res => {
				this.categories = res.data
			});
		},

		onShow() {
			// #ifdef MP-BAIDU
			swan.setPageInfo({
				title: this.setting.title,
				description: this.setting.description,
				keywords: '文章分类',
			});
			// #endif
		},

		onShareAppMessage() {
			return {
				title: getApp().globalData.appName,
				path: 'pages/index/index'
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: getApp().globalData.appName
			};
		},
		// #endif

		methods: {
			handlerCategoryClick(e) {
				let cat_id = e.currentTarget.dataset.id;
				let cat_name = e.currentTarget.dataset.name;
				uni.navigateTo({
					url: '/pages/list/list?cat_id=' + cat_id + '&title=' + cat_name
				});
			}
		}
	};
</script>
<style lang="scss" scoped>
	.jiangqie-searchbox {
		width: 100%;
		height: 92rpx;
		padding: 0 30rpx;
		box-sizing: border-box;
		background: #fff;
		display: flex;
		align-items: center;
		justify-content: center;
		position: fixed;
		left: 0;
		top: 0;
		/* #ifdef H5 */
		top: calc(88rpx + constant(safe-area-inset-top));
		top: calc(88rpx + env(safe-area-inset-top));
		/* #endif */
		z-index: 100;
	}

	.jiangqie-searchbox::after {
		content: '';
		position: absolute;
		border-bottom: 1rpx solid #d2d2d2;
		-webkit-transform: scaleY(0.5);
		transform: scaleY(0.5);
		bottom: 0;
		right: 0;
		left: 0;
	}

	.jiangqie-search-input {
		width: 100%;
		height: 60rpx;
		background: #f1f1f1;
		border-radius: 30rpx;
		font-size: 26rpx;
		color: #999;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.jiangqie-search-text {
		padding-left: 16rpx;
	}


	.right-box {
		padding: 20rpx 20rpx 0 20rpx;
		padding-bottom: env(safe-area-inset-bottom);
		box-sizing: border-box;
	}

	.page-view {
		width: 100%;
		overflow: hidden;
	}

	.sortbox {
		background: #fff;
		width: 100%;
		box-sizing: border-box;
		border-radius: 12rpx;
		padding: 20rpx 20rpx 20rpx 180rpx;
		overflow: hidden;
		border-bottom: 1rpx solid #DDD;
	}

	.sortimage {
		height: 120rpx;
		width: 160rpx;
		float: left;
		border-radius: 12rpx;
		margin-left: -160rpx;
	}

	.sorttitle {
		overflow: hidden;
		padding: 20rpx 30rpx 0;
		font-size: 32rpx;
		font-weight: 600;
		color: #333;
	}

	.sortsummary {
		overflow: hidden;
		padding: 10rpx 30rpx 20rpx;
		font-size: 24rpx;
		font-weight: 400;
		color: #999;
	}

	.categorie-header {
		height: 280rpx;
		width: 100%;
		position: relative;
	}

	.categorie-header image {
		height: 280rpx;
		width: 100%;
		position: absolute;
		z-index: 1;
	}

	.categorie-text {
		position: absolute;
		width: 610rpx;
		left: 30rpx;
		bottom: 20rpx;
		color: #FFF;
		z-index: 99;
		padding: 20rpx 40rpx;
		background: rgba(0, 0, 0, 0.4);
		border-radius: 20rpx;
	}

	.categorie-text text {
		font-size: 36rpx;
		color: #FFF;
		font-weight: 500;
		display: block;
		line-height: 40rpx;
	}

	.categorie-text view {
		font-size: 24rpx;
		color: #FFF;
		font-weight: 400;
		line-height: 40rpx;
	}
</style>
