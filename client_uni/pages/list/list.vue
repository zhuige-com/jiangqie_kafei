<template>
	<view>
		<!--新闻列表——常规-->
		<view class="container">
			<view class="jiangqie-news-view">

				<template v-if="posts.length > 0">
					<template v-for="(item, index) in posts">
						<view v-if="index%5==4" :key="index" class="jiangqie-news-block image-wide" :data-id="item.id"
							@tap.stop="handlerArticleClick">
							<image :src="item.thumbnail" mode="aspectFill" class="jiangqie-news-image"></image>
							<view class="jiangqie-news-text">
								<view :class="'jiangqie-news-title ' + (item.excerpt?'':'jiangqie-news-no-excerpt')">
									{{item.title}}
								</view>
								<view v-if="item.excerpt" class="jiangqie-news-describe">{{item.excerpt}}</view>
								<view class="jiangqie-news-info">
									<view class="jiangqie-news-cmt">
										<image src="/static/images/ctm2.png" mode="aspectFill"></image>{{item.views}}
									</view>
									<text v-for="(item, index2) in item.tags" :key="index2"
										class="jiangqie-news-tag">{{item.name}}</text>
									<text class="jiangqie-news-time">{{item.time}}</text>
								</view>
							</view>
						</view>
						<view v-else :key="index" class="jiangqie-news-block" :data-id="item.id"
							@tap.stop="handlerArticleClick">
							<image :src="item.thumbnail" mode="aspectFill" class="jiangqie-news-image"></image>
							<view class="jiangqie-news-text">
								<view :class="'jiangqie-news-title ' + (item.excerpt?'':'jiangqie-news-no-excerpt')">
									{{item.title}}
								</view>
								<view v-if="item.excerpt" class="jiangqie-news-describe">{{item.excerpt}}</view>
								<view class="jiangqie-news-info">
									<view class="jiangqie-news-cmt">
										<image src="/static/images/ctm2.png" mode="aspectFill"></image>{{item.views}}
									</view>
									<text v-for="(item, index2) in item.tags" :key="index2"
										class="jiangqie-news-tag">{{item.name}}</text>
									<text class="jiangqie-news-time">{{item.time}}</text>
								</view>
							</view>
						</view>
					</template>

					<!--加载loadding-->
					<jiangqie-loadmore :visible="loadding"></jiangqie-loadmore>
					<jiangqie-nomore :visible="!pullUpOn"></jiangqie-nomore>
					<!--加载loadding-->

				</template>
				<template v-else-if="loaded">
					<jiangqie-no-data></jiangqie-no-data>
				</template>

			</view>
		</view>

		<!--加载框 start-->
		<jiangqie-loading v-if="loading"></jiangqie-loading>
		<!--加载框 end-->
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
	const Constants = require("@/utils/constants.js");
	const Util = require("@/utils/util.js");
	const Api = require("@/utils/api.js");
	const Rest = require("@/utils/rest.js");
	import JiangqieLoading from "@/components/loading/loading";
	import JiangqieLoadmore from "@/components/loadmore/loadmore";
	import JiangqieNomore from "@/components/nomore/nomore";
	import JiangqieNoData from "@/components/nodata/nodata";

	export default {
		data() {
			return {
				title: undefined,
				
				posts: [],
				loadding: false,
				pullUpOn: true,
				loaded: false,
				tag_id: undefined,
				cat_id: undefined,
				search: undefined,
				track: undefined,
				loading: false,
			};
		},

		components: {
			JiangqieLoading,
			JiangqieLoadmore,
			JiangqieNomore,
			JiangqieNoData,
		},

		props: {},

		/**
		 * 生命周期函数--监听页面加载
		 */
		onLoad(options) {
			if (options.cat_id) {
				//分类
				this.title = options.title;
				uni.setNavigationBarTitle({
					title: options.title
				});
				this.cat_id = options.cat_id;
			} else if (options.tag_id) {
				//标签
				this.title = options.title;
				uni.setNavigationBarTitle({
					title: options.title
				});
				this.tag_id = options.tag_id;
			} else if (options.search) {
				//搜索
				this.title = '搜索【' + options.search + '】';
				uni.setNavigationBarTitle({
					title: this.title
				});
				this.search = options.search;
			} else if (options.track) {
				//我的足迹
				let title = '我的浏览';

				if (options.track == 'likes') {
					title = '我的点赞';
				} else if (options.track == 'favorites') {
					title = '我的收藏';
				} else if (options.track == 'comments') {
					title = '我的评论';
				}

				this.title = title;
				uni.setNavigationBarTitle({
					title: title
				});
				this.track = options.track;
			} else {
				//最新
				this.title = '最新文章';
				uni.setNavigationBarTitle({
					title: '最新文章'
				});
			}
		},

		onShow() {
			// #ifdef MP-BAIDU
			swan.setPageInfo({
				title: this.title,
				description: this.title + '相关的文章',
				keywords: this.title,
			});
			// #endif
			
			this.loadPost(true);
		},

		onPullDownRefresh() {},

		onReachBottom() {
			if (!this.pullUpOn) {
				return;
			}

			this.loadPost(false);
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
			handlerArticleClick(e) {
				let post_id = e.currentTarget.dataset.id;
				uni.navigateTo({
					url: '/pages/article/article?post_id=' + post_id
				});
			},

			loadPost(refresh) {
				this.loadding = true;
				let offset = 0;
				if (!refresh) {
					offset = this.posts.length;
				}

				let url = '';
				let params = {
					offset: offset
				};

				if (this.cat_id !== undefined) {
					url = Api.JIANGQIE_POSTS_CATEGORY;
					params.cat_id = this.cat_id;
				} else if (this.tag_id !== undefined) {
					url = Api.JIANGQIE_POSTS_TAG;
					params.tag_id = this.tag_id;
				} else if (this.search !== undefined) {
					url = Api.JIANGQIE_POSTS_SEARCH;
					params.search = this.search;
				} else if (this.track !== undefined) {
					url = Api.JIANGQIE_POSTS_MY;
					params.track = this.track;
				} else {
					url = Api.JIANGQIE_POSTS_LAST;
				}

				Rest.get(url, params).then(res => {
					this.loaded = true;
					this.loadding = false;
					this.posts = (refresh ? res.data : this.posts.concat(res.data));
					this.pullUpOn = res.data.length >= Constants.JQ_PER_PAGE_COUNT;
				});
			},
		}
	};
</script>

<style lang="scss" scoped>
	.container {
		display: flex;
		flex-direction: column;
		box-sizing: border-box;
		padding: 0 30rpx;
		padding-bottom: env(safe-area-inset-bottom);
	}

	/** jiangqie-news-block **/
	.jiangqie-news-type {
		font-size: 40rpx;
		font-weight: 600;
		color: #555;
		padding: 16rpx 0 0;
	}

	.jiangqie-news-type text {
		float: right;
		font-size: 28rpx;
		font-weight: 200;
		color: #CCC;
	}

	.jiangqie-news-block {
		padding: 30rpx 0;
		min-height: 160rpx;
		border-bottom: 1rpx solid #DDD;
		padding-right: 260rpx;
		clear: right;
	}

	.jiangqie-news-image {
		float: right;
		height: 160rpx;
		width: 240rpx;
		margin-right: -260rpx;
		border-radius: 20rpx;
		box-shadow: 5rpx 5rpx 20rpx rgba(0, 0, 0, 0.2);
	}

	.jiangqie-news-text {
		text-align: left;
	}

	.jiangqie-news-title {
		font-size: 32rpx;
		color: #333;
		font-weight: 400;
		line-height: 50rpx;
		overflow: hidden;
	}

	.jiangqie-news-no-excerpt {
		margin-bottom: 30rpx;
		height: 100rpx;
	}

	.jiangqie-news-describe {
		font-size: 24rpx;
		color: #999;
		line-height: 30rpx;
		margin-bottom: 18rpx;
	}

	.jiangqie-news-info {
		font-size: 20rpx;
		color: #CCC;
		vertical-align: bottom;
		height: 24rpx;
	}

	.jiangqie-news-cmt {
		float: right;
		padding-right: 4rpx;

	}

	.jiangqie-news-cmt image {
		vertical-align: middle;
		height: 24rpx;
		width: 24rpx;
		margin-right: 8rpx;
		border-radius: 10rpx;
	}

	.jiangqie-news-tag {
		float: left;
		line-height: 32rpx;
		height: 30rpx;
		padding: 0 16rpx;
		border: 1rpx solid #AAA;
		border-radius: 26rpx;
		margin-right: 12rpx;
	}

	.jiangqie-news-time {
		float: left;
		line-height: 30rpx;
		height: 30rpx;
		vertical-align: middle;
		padding-left: 10rpx;
	}

	.image-wide {
		padding-right: 0;
	}

	.image-wide .jiangqie-news-image {
		height: 320rpx;
		width: 690rpx;
		margin-right: 0rpx;
		margin-bottom: 10rpx;
		clear: both;
		float: none;
	}

	.image-wide .jiangqie-news-title {
		height: auto;
		margin-bottom: 10rpx;
	}
</style>
