<template>
	<view>
		<view class="jiangqie-timeline-head">
			<image v-if="setting.background && setting.background.length>0" :src="setting.background" mode="aspectFill">
			</image>
			<view v-if="setting.title.length>0 || setting.description.length>0" class="timeline-head-text">
				<text>{{setting.title}}</text>
				<view class="timeline-head-describe">{{setting.description}}</view>
			</view>
		</view>

		<scroll-view scroll-x scroll-with-animation class="tab-view" :scroll-left="0">
			<view v-for="(item, index) in tabbar" :key="index" class="tab-bar-item"
				:class="(currentTab==index ? 'active' : '')" :data-current="index" @tap.stop="swichNav">
				<text class="tab-bar-title">{{item}}</text>
			</view>
		</scroll-view>

		<view
			:style="tl_background?'background: url(' + tl_background + ') repeat-y; background-position: -30rpx 0;':''">
			<template v-if="posts.length > 0">
				<view class="jiangqie-timeline-view">

					<view v-for="(item, index) in posts" :key="index" :data-id="item.id" class="jiangqie-timeline-block"
						@tap.stop="handlerHotArticle">
						<view class="jiangqie-timeline-times">
							<image src="/static/images/timelinedot.png" mode="aspectFill"></image>
							<text>{{item.time}}</text>
						</view>
						<view class="jiangqie-timeline-box timeline-single">
							<view class="jiangqie-timeline-text">
								<view class="jiangqie-timeline-title">{{item.title}}</view>
								<view class="jiangqie-timeline-info">
									<view class="jiangqie-timeline-cmt">
										<template v-if="currentTab==0">
											<image src="/static/images/ctm2.png" mode="aspectFill"></image>
											{{item.views?item.views:''}}
										</template>
										<template v-else-if="currentTab==1">
											<image src="/static/images/laud.png" mode="aspectFill"></image>
											{{item.likes?item.likes:''}}
										</template>
										<template v-else-if="currentTab==2">
											<image src="/static/images/ctm3.png" mode="aspectFill"></image>
											{{item.favorites?item.favorites:''}}
										</template>
										<template v-else-if="currentTab==3">
											<image src="/static/images/ctm.png" mode="aspectFill"></image>
											{{item.comment_count?item.comment_count:''}}
										</template>
									</view>
									<text v-for="(item, index2) in item.tags" :key="index2"
										class="jiangqie-timeline-tag">{{item.name}}</text>
								</view>
							</view>
							<view class="jiangqie-timeline-image">
								<image :src="item.thumbnail" mode="aspectFill"></image>
							</view>
						</view>
					</view>

					<!--加载loadding-->
					<jiangqie-loadmore :visible="loadding"></jiangqie-loadmore>
					<jiangqie-nomore :visible="!pullUpOn"></jiangqie-nomore>
					<!--加载loadding-->

				</view>

			</template>
			<template v-else-if="loaded">
				<jiangqie-no-data></jiangqie-no-data>
			</template>
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
	 * Copyright © 2020-2024 www.zhuige.com All rights reserved.
	 */
	const Constants = require("@/utils/constants.js");
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
			this.default = {
				background: '',
				title: '热门标题，请在后台修改',
				description: '热门描述，请在后台修改'
			};
			this.sorts = ['views', 'likes', 'favorites', 'comments'];

			return {
				setting: {
					background: "",
					title: "",
					description: ""
				},

				tabbar: ['浏览数', '点赞数', '收藏数', '评论数'],
				currentTab: 0,
				posts: [],

				loadding: false,
				pullUpOn: true,
				loaded: false,

				tl_background: '',
			};
		},

		onLoad(options) {
			//获取配置
			Rest.get(Api.JIANGQIE_SETTING_HOT).then(res => {
				this.setting = {
					background: res.data.background,
					title: res.data.title ? res.data.title : this.default.title,
					description: res.data.description ? res.data.description : this.default
						.description
				};
				this.tl_background = res.data.tl_background;

				// #ifdef MP-BAIDU
				swan.setPageInfo({
					title: this.setting.title,
					description: this.setting.description,
					keywords: '热榜',
				});
				// #endif
			});
			this.loadPosts(true);
		},

		onShow() {
			// #ifdef MP-BAIDU
			swan.setPageInfo({
				title: this.setting.title,
				description: this.setting.description,
				keywords: '热榜',
			});
			// #endif
		},

		onReachBottom() {
			if (!this.pullUpOn) {
				return;
			}

			this.loadPosts(false);
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
			 * 点击标题切换当前页时改变样式
			 */
			swichNav(e) {
				let cur = e.currentTarget.dataset.current;

				if (this.currentTab == cur) {
					return false;
				}

				this.currentTab = cur;
				this.loadPosts(true);
			},

			/**
			 * 点击 文章
			 */
			handlerHotArticle(e) {
				let post_id = e.currentTarget.dataset.id;
				uni.navigateTo({
					url: '/pages/article/article?post_id=' + post_id
				});
			},

			/**
			 * 加载数据
			 */
			loadPosts(refresh) {
				this.loadding = true;
				Rest.get(Api.JIANGQIE_POSTS_HOT, {
					'offset': refresh ? 0 : this.posts.length,
					'sort': this.sorts[this.currentTab]
				}).then(res => {
					this.loaded = true;
					this.loadding = false;
					this.posts = (refresh ? res.data : this.posts.concat(res.data)),
						this.pullUpOn = (res.data.length >= Constants.JQ_PER_PAGE_COUNT)
				});
			}

		}
	};
</script>

<style lang="scss" scoped>
	.jiangqie-timeline-view {
		text-align: left;
		padding-left: 66rpx;
		padding-right: 30rpx;
	}

	.jiangqie-timeline-block {
		display: block;
		position: relative;
		padding-top: 20rpx;
		margin-top: 30rpx;
		border-bottom: 1rpx solid #DDD;
	}

	.jiangqie-timeline-times {
		position: absolute;
		top: -18rpx;
		left: -38rpx;
	}

	.jiangqie-timeline-times image {
		height: 17rpx;
		width: 17rpx;
		margin-right: 20rpx;
	}

	.jiangqie-timeline-times text {
		font-size: 24rpx;
		font-weight: 400;
		color: #BBB;
	}

	.jiangqie-timeline-box {
		padding: 30rpx 0rpx;
		background: #FFF;
	}

	.jiangqie-timeline-image {
		height: 180rpx;
		width: 180rpx;
	}

	.jiangqie-timeline-image image {
		height: 100%;
		width: 100%;
		border-radius: 16rpx;
	}

	.jiangqie-timeline-text {
		text-align: left;
		width: 440rpx;
		overflow: hidden;
	}

	.jiangqie-timeline-title {
		font-size: 32rpx;
		color: #333;
		font-weight: 400;
		line-height: 46rpx;
		margin-bottom: 24rpx;
		max-height: 92rpx;
		overflow: hidden;
	}

	.jiangqie-timeline-describe {
		font-size: 24rpx;
		color: #999;
		line-height: 30rpx;
	}

	.jiangqie-timeline-info {
		font-size: 22rpx;
		color: #AAA;
		vertical-align: bottom;
		height: 26rpx;
	}

	.jiangqie-timeline-cmt {
		float: right;
		padding-right: 4rpx;

	}

	.jiangqie-timeline-cmt image {
		vertical-align: middle;
		height: 26rpx;
		width: 26rpx;
		margin-right: 8rpx;
		border-radius: 10rpx;
		margin-bottom: 8rpx;
	}

	.jiangqie-timeline-tag {
		float: left;
		line-height: 32rpx;
		height: 30rpx;
		padding: 0 16rpx;
		border: 1rpx solid #AAA;
		border-radius: 26rpx;
		margin-right: 12rpx;
	}

	.jiangqie-news-tl-redline {
		padding: 20rpx;
		text-align: center;
	}

	.jiangqie-news-tl-redlinebox {
		display: inline-table;
		padding: 0 30rpx 4rpx;
		background: #FFE8E8;
		border-radius: 40rpx;
		left: 50%;

	}

	.jiangqie-news-tl-redlinebox text {
		font-weight: 400;
		font-size: 22rpx;
		color: #CC6565;
		padding: 0 10rpx;
	}

	.timeline-single {
		padding-top: 10rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;

		// 开启该参数,可以调整图片，文字的左右浮动方式
		// flex-direction: row-reverse;
	}

	.timeline-single .jiangqie-timeline-image {
		height: 180rpx;
		width: 180rpx;
	}

	.timeline-big .jiangqie-timeline-image {
		width: 100%;
		margin-bottom: 30rpx;
	}

	.timeline-mult {
		clear: both;
		margin-bottom: 30rpx;
		min-height: 180rpx;
	}

	.timeline-mult .jiangqie-timeline-image {
		float: left;
		margin-right: 27rpx;
		width: 180rpx;
	}

	.timeline-mult .jiangqie-timeline-image:last-child {
		margin-right: 0;
	}

	.jiangqie-timeline-head {
		height: 280rpx;
		width: 100%;
		position: relative;
	}

	.jiangqie-timeline-head image {
		height: 280rpx;
		width: 100%;
	}

	.timeline-head-text {
		width: 610rpx;
		background: rgba(0, 0, 0, 0.4);
		padding: 20rpx 40rpx;
		position: absolute;
		bottom: 20rpx;
		left: 30rpx;
		border-radius: 16rpx;
	}

	.timeline-head-text text {
		color: #FFF;
		font-weight: 500;
		line-height: 40rpx;
		font-size: 36rpx;
	}

	.timeline-head-describe {
		color: #FFF;
		font-weight: 400;
		font-size: 24rpx;
	}

	/*tabbar start*/
	.tab-more {
		height: 100rpx;
		width: 120rpx;
		position: absolute;
		right: 0;
	}

	.tab-more image {
		height: 100rpx;
		width: 120rpx;
	}

	::-webkit-scrollbar {
		width: 0;
		height: 0;
		color: transparent;
	}

	.tab-view::before {
		content: '';
		position: absolute;
		-webkit-transform: scaleY(0.5);
		transform: scaleY(0.5);
		bottom: 0;
		right: 0;
		left: 0;
	}

	.tab-view {
		position: -webkit-sticky;
		position: sticky;
		top: var(--window-top);

		width: 100%;
		height: 100rpx;
		overflow: hidden;
		box-sizing: border-box;
		left: 0;
		z-index: 99;
		background: #fff;
		white-space: nowrap;
		border-bottom: 1rpx solid #DDD;
	}

	.tab-bar-item {
		padding: 0;
		height: 100rpx;
		width: 15%;
		line-height: 100rpx;
		margin: 0 38rpx;
		display: inline-block;
		text-align: center;
		box-sizing: border-box;
	}

	.tab-bar-title {
		height: 100rpx;
		line-height: 100rpx;
		font-size: 32rpx;
		color: #999;
		font-weight: 400;
	}

	.active {
		border-bottom: 6rpx solid #E6321D;
	}

	.active .tab-bar-title {
		color: #E6321D !important;
		font-size: 36rpx;
		font-weight: bold;
	}

	/*tabbar end*/

	.scoll-y {
		height: 100%;
	}
</style>