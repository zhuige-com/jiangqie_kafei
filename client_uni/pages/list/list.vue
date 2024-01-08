<template>
	<!--新闻列表——常规-->
	<view class="container">

		<template v-if="posts.length > 0">
			<template v-for="(item, index) in posts">
				<view v-if="index%5==4" :key="index" class="jiangqie-news-block image-wide" :data-id="item.id"
					@tap.stop="handlerArticleClick">
					<view class="jiangqie-news-image">
						<image :src="item.thumbnail" mode="aspectFill"></image>
					</view>
					<view class="jiangqie-news-text">
						<view :class="'jiangqie-news-title ' + (item.excerpt?'':'jiangqie-news-no-excerpt')">
							{{item.title}}
						</view>
						<view v-if="item.excerpt" class="jiangqie-news-describe">{{item.excerpt}}</view>
						<view class="jiangqie-news-info">
							<text v-for="(item, index2) in item.tags" :key="index2"
								class="jiangqie-news-tag">{{item.name}}</text>
							<text class="jiangqie-news-time">{{item.time}}</text>
							<view class="jiangqie-news-cmt">
								<image src="/static/images/ctm2.png" mode="aspectFill"></image>{{item.views}}
							</view>
						</view>
					</view>
				</view>
				<view v-else :key="index" class="jiangqie-news-block" :data-id="item.id"
					@tap.stop="handlerArticleClick">
					<view class="jiangqie-news-image">
						<image :src="item.thumbnail" mode="aspectFill"></image>
					</view>
					<view class="jiangqie-news-text">
						<view :class="'jiangqie-news-title ' + (item.excerpt?'':'jiangqie-news-no-excerpt')">
							{{item.title}}
						</view>
						<view v-if="item.excerpt" class="jiangqie-news-describe">{{item.excerpt}}</view>
						<view class="jiangqie-news-info">
							<text v-for="(item, index2) in item.tags" :key="index2"
								class="jiangqie-news-tag">{{item.name}}</text>
							<text class="jiangqie-news-time">{{item.time}}</text>
							<view class="jiangqie-news-cmt">
								<image src="/static/images/ctm2.png" mode="aspectFill"></image>{{item.views}}
							</view>
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

	const Constants = require("@/utils/constants.js");
	const Util = require("@/utils/util.js");
	const Api = require("@/utils/api.js");
	const Rest = require("@/utils/rest.js");
	import JiangqieLoadmore from "@/components/loadmore/loadmore";
	import JiangqieNomore from "@/components/nomore/nomore";
	import JiangqieNoData from "@/components/nodata/nodata";

	export default {
		components: {
			JiangqieLoadmore,
			JiangqieNomore,
			JiangqieNoData
		},
		
		data() {
			this.tag_id = undefined;
			this.cat_id = undefined;
			this.search = undefined;
			this.track = undefined;

			return {
				title: undefined,

				posts: [],
				loadding: false,
				pullUpOn: true,
				loaded: false,
			};
		},

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
			/**
			 * 点击打开文章
			 */
			handlerArticleClick(e) {
				let post_id = e.currentTarget.dataset.id;
				uni.navigateTo({
					url: '/pages/article/article?post_id=' + post_id
				});
			},

			/**
			 * 加载 文章列表
			 */
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
		position: relative;
		display: flex;
		align-items: center;
		justify-content: flex-end;
		border-bottom: 1rpx solid #DDD;
		padding: 20rpx 0;
		flex-direction: row-reverse;
	}

	.jiangqie-news-block:last-child {
		border: none;
	}

	.jiangqie-news-stick-image {
		position: absolute;
		height: 60rpx;
		width: 60rpx;
		left: 0rpx;
		top: 0rpx;
	}

	.jiangqie-news-image {
		flex: 0 0 260rpx;
		height: 200rpx;
		width: 260rpx;
		border-radius: 8rpx;
	}

	.jiangqie-news-image image {
		height: 100%;
		width: 100%;
		border-radius: 8rpx;
	}

	.jiangqie-news-text {
		text-align: left;
		padding-right: 20rpx;
		width: 100%;
		overflow: hidden;
	}

	.jiangqie-news-title {
		font-size: 32rpx;
		color: #333;
		font-weight: 600;
		line-height: 44rpx;
		max-height: 88rpx;
		overflow: hidden;
		margin-bottom: 12rpx;
	}

	.jiangqie-news-no-excerpt {
		margin-bottom: 30rpx;
		height: 100rpx;
	}

	.jiangqie-news-describe {
		font-size: 26rpx;
		color: #999;
		line-height: 30rpx;
		max-height: 30rpx;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
		margin-bottom: 18rpx;
	}

	.jiangqie-news-info {
		font-size: 22rpx;
		color: #BBB;
		height: 26rpx;
		display: flex;
		align-items: center;
	}

	.jiangqie-news-cmt {
		margin-right: 12rpx;
		display: flex;
		align-items: center;
	}

	.jiangqie-news-cmt image {
		height: 24rpx;
		width: 24rpx;
		margin-right: 8rpx;
		border-radius: 10rpx;
	}

	.jiangqie-news-tag {
		line-height: 32rpx;
		height: 30rpx;
		padding: 0 16rpx;
		border: 1rpx solid #AAA;
		border-radius: 26rpx;
		margin-right: 12rpx;
		display: none;
	}

	.jiangqie-news-time {
		float: left;
		line-height: 30rpx;
		height: 30rpx;
		vertical-align: middle;
		padding-right: 10rpx;
	}

	.image-wide {
		padding-right: 0;
		flex-wrap: wrap;
	}

	.image-wide .jiangqie-news-image {
		flex: none;
		height: 320rpx;
		width: 690rpx;
		margin-right: 0rpx;
		margin-bottom: 10rpx;
	}

	.image-wide .jiangqie-news-text {
		padding-left: 0;
	}

	.image-wide .jiangqie-news-title {
		height: auto;
	}
</style>