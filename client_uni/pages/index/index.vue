<template>
	<view>
		<view class="jiangqie-top-bg" :style="background?'background-image: url(' + background + ');':''">

			<uni-nav-bar :fixed="true" :statusBar="true" backgroundColor="#E0301F">
				<template slot="left">
					<view class="jiangqie-logo">
						<image :src="logo"></image>
					</view>
				</template>
				<view class="jiangqie-btn-search" @click="handlerSearchClick">
					<uni-icons class="input-uni-icon" type="search" size="18" color="#f8b8b8" />
					<text>点我搜索</text>
				</view>
			</uni-nav-bar>

			<scroll-view scroll-x scroll-with-animation class="tab-view" :scroll-left="scrollLeft">
				<view v-for="(item, index) in topNav" :key="index"
					:class="'tab-bar-item ' + (currentTab==index ? 'active' : '')" :data-current="index"
					@tap.stop="swichNav">
					<text class="tab-bar-title">{{item.name}}</text>
				</view>
				<view class="tab-bar-item">
					<text class="tab-bar-title">.</text>
				</view>
			</scroll-view>

			<view class="tab-more" @tap.stop="handlerTabMoreClick">
				<image src="/static/images/tabmorered.png" mode="aspectFill"></image>
			</view>

			<view class="tab-content" style="padding-top: 120rpx;">
				<template v-if="currentTab==0">

					<!--banner-->
					<view v-if="slide && slide.length>0" class="jiangqie-banner-box">
						<swiper indicator-dots="true" autoplay="true" interval="5000" duration="150"
							class="jiangqie-banner-swiper" :circular="true" previous-margin="15rpx" next-margin="15rpx"
							@change="handlerSlideChange">
							<swiper-item v-for="(item, index) in slide" :key="index" class="jiangqie-banner-item"
								:data-id="item.id" @tap.stop="handlerArticleClick">
								<image :src="item.thumbnail"
									:class="'jiangqie-slide-image ' + ((current==index || (current==undefined && index==0))?'':'jiangqie-banner-scale')"
									mode="aspectFill"></image>
							</swiper-item>
						</swiper>
					</view>
					<!--banner-->

					<view class="container">
						<!--图标模块 可单独设置背景色、背景图、图标图-->
						<view v-if="iconNav && iconNav.length>0" class="jiangqie-news-icon">
							<view v-for="(item, index) in iconNav" :key="index" class="jiangqie-news-iconbox"
								:data-link="item.link" @tap.stop="handlerIconNavClick">
								<view class="jiangqie-news-iconbg">
									<image class="jiangqie-news-iconimg" :src="item.icon"></image>
								</view>
								<text>{{item.title}}</text>
							</view>
						</view>

						<!--推荐模块-->
						<view v-if="actives" class="jiangqie-news-view">
							<view class="jiangqie-news-lightbox jiangqie-news-lightbox-main"
								:data-link="actives.left.link" @tap.stop="handlerActiveClick">
								<image class="jiangqie-news-lightimg" :src="actives.left.image" mode="aspectFill">
								</image>
								<view class="jiangqie-news-lighttext">
									<text class="jiangqie-news-lihgttitle">{{actives.left.title}}</text>
								</view>
							</view>
							<view class="jiangqie-news-lightbox jiangqie-news-lightbox-side"
								:data-link="actives.right_top.link" @tap.stop="handlerActiveClick">
								<image class="jiangqie-news-lightimg" :src="actives.right_top.image" mode="aspectFill">
								</image>
								<view class="jiangqie-news-lighttext">
									<text class="jiangqie-news-lihgttitle">{{actives.right_top.title}}</text>
								</view>
							</view>
							<view class="jiangqie-news-lightbox jiangqie-news-lightbox-side2"
								:data-link="actives.right_down.link" @tap.stop="handlerActiveClick">
								<image class="jiangqie-news-lightimg" :src="actives.right_down.image" mode="aspectFill">
								</image>
								<view class="jiangqie-news-lighttext">
									<text class="jiangqie-news-lihgttitle">{{actives.right_down.title}}</text>
								</view>
							</view>
						</view>

						<!--新闻列表——热门-->
						<view v-if="hot && hot.length>0" class="jiangqie-news-view jiangqie-news-hot">
							<view class="jiangqie-news-type">
								热门推荐<text>滑动查看</text>
							</view>
							<scroll-view class="jiangqie-news-scroll-x-box" scroll-x="true">
								<view v-for="(item, index) in hot" :key="index" class="jiangqie-hot-scroll-box"
									:data-id="item.id" @tap.stop="handlerArticleClick">
									<image :src="item.thumbnail" mode="aspectFill" class="jiangqie-hot-scroll-image">
									</image>
									<view class="jiangqie-hot-scroll-title">{{item.title}}</view>
								</view>
							</scroll-view>
						</view>

						<!--新闻列表——常规-->
						<view class="jiangqie-news-view">
							<view v-if="postsLast && postsLast.length>0" class="jiangqie-news-type">
								最新文章
							</view>
							<template v-for="(item, index) in postsLast">
								<view v-if="(listMode==1 && false) || (listMode==2) || (listMode==3 && index%5==4)"
									:key="index" class="jiangqie-news-block image-wide" :data-id="item.id"
									@tap.stop="handlerArticleClick">
									<image :src="item.thumbnail" mode="aspectFill" class="jiangqie-news-image"></image>
									<view class="jiangqie-news-text">
										<view
											:class="'jiangqie-news-title ' + (item.excerpt?'':'jiangqie-news-no-excerpt')">
											<text v-if="item.stick==1">置顶</text>
											{{item.title}}
										</view>
										<view v-if="item.excerpt" class="jiangqie-news-describe">{{item.excerpt}}</view>
										<view class="jiangqie-news-info">
											<view class="jiangqie-news-cmt">
												<image src="/static/images/ctm2.png" mode="aspectFill"></image>
												{{item.views}}
											</view>
											<text v-for="(item, index2) in item.tags" :key="index2"
												class="jiangqie-news-tag">{{item.name}}</text>
											<text class="jiangqie-news-time">{{item.time}}</text>
										</view>
									</view>
									<!-- <image wx:if="{{item.stick==1}}" src="../../images/set_top.png" mode="aspectFill" class="jiangqie-news-stick-image"></image> -->
								</view>
								<view v-else :key="index" class="jiangqie-news-block" :data-id="item.id"
									@tap.stop="handlerArticleClick">
									<image :src="item.thumbnail" mode="aspectFill" class="jiangqie-news-image"></image>
									<view class="jiangqie-news-text">
										<view
											:class="'jiangqie-news-title ' + (item.excerpt?'':'jiangqie-news-no-excerpt')">
											<text v-if="item.stick==1">置顶</text>
											{{item.title}}
										</view>
										<view v-if="item.excerpt" class="jiangqie-news-describe">{{item.excerpt}}</view>
										<view class="jiangqie-news-info">
											<view class="jiangqie-news-cmt">
												<image src="/static/images/ctm2.png" mode="aspectFill"></image>
												{{item.views}}
											</view>
											<text v-for="(item, index2) in item.tags" :key="index2"
												class="jiangqie-news-tag">{{item.name}}</text>
											<text class="jiangqie-news-time">{{item.time}}</text>
										</view>
									</view>
								</view>
							</template>
						</view>
					</view>
					<!--加载loadding-->
					<jiangqie-loadmore :visible="loaddingLast"></jiangqie-loadmore>
					<jiangqie-nomore :visible="!pullUpOnLast"></jiangqie-nomore>
					<!--加载loadding-->
				</template>

				<template v-else>
					<view class="container">
						<view class="jiangqie-news-view">
							<template v-for="(item, index) in posts">
								<view v-if="(listMode==1 && false) || (listMode==2) || (listMode==3 && index%5==4)"
									:key="index" class="jiangqie-news-block image-wide" :data-id="item.id"
									@tap.stop="handlerArticleClick">
									<image :src="item.thumbnail" mode="aspectFill" class="jiangqie-news-image"></image>
									<view class="jiangqie-news-text">
										<view
											:class="'jiangqie-news-title ' + (item.excerpt?'':'jiangqie-news-no-excerpt')">
											{{item.title}}
										</view>
										<view v-if="item.excerpt" class="jiangqie-news-describe">{{item.excerpt}}</view>
										<view class="jiangqie-news-info">
											<view class="jiangqie-news-cmt">
												<image src="/static/images/ctm2.png" mode="aspectFill"></image>
												{{item.views}}
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
										<view
											:class="'jiangqie-news-title ' + (item.excerpt?'':'jiangqie-news-no-excerpt')">
											{{item.title}}
										</view>
										<view v-if="item.excerpt" class="jiangqie-news-describe">{{item.excerpt}}</view>
										<view class="jiangqie-news-info">
											<view class="jiangqie-news-cmt">
												<image src="/static/images/ctm2.png" mode="aspectFill"></image>
												{{item.views}}
											</view>
											<text v-for="(item, index2) in item.tags" :key="index2"
												class="jiangqie-news-tag">{{item.name}}</text>
											<text class="jiangqie-news-time">{{item.time}}</text>
										</view>
									</view>
								</view>
							</template>
						</view>
					</view>
					<!--加载loadding-->
					<jiangqie-loadmore :visible="loadding"></jiangqie-loadmore>
					<jiangqie-nomore :visible="!pullUpOn"></jiangqie-nomore>
					<!--加载loadding-->
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
	const Api = require("@/utils/api.js");
	const Util = require("@/utils/util.js");
	const Rest = require("@/utils/rest.js");

	import JiangqieLoading from "@/components/loading/loading";
	import JiangqieLoadmore from "@/components/loadmore/loadmore";
	import JiangqieNomore from "@/components/nomore/nomore";
	import uniNavBar from '@/components/uni-nav-bar/uni-nav-bar.vue'

	export default {
		data() {
			return {
				bd_title: undefined,
				bd_description: undefined,
				bd_keywords: undefined,
				
				statusBarH: this.statusBar,
				customBarH: 50,

				logo: '',
				background: undefined,

				//顶部导航
				topNav: [{
					id: 0,
					name: '头条'
				}],
				currentTab: 0,
				//预设当前项的值

				//幻灯片
				slide: [],

				//图片导航
				iconNav: [],

				//热门文章
				hot: [],

				//热门tab
				postsLast: [],
				loaddingLast: false,
				pullUpOnLast: true,

				//其他tab
				posts: [],
				loadding: false,
				pullUpOn: true,

				//列表模式
				listMode: 3,

				scrollLeft: "",
				current: "",
				undefined: "",
				actives: undefined,
				loading: false,
				
				interstitialAd: undefined,
				wx_ad: undefined,
			};
		},

		components: {
			JiangqieLoading,
			JiangqieLoadmore,
			JiangqieNomore,
			uniNavBar
		},

		props: {},

		onLoad(options) {
			Rest.get(Api.JIANGQIE_SETTING_HOME).then(res => {
				let logo = "/static/images/logo.png";
				if (res.data.logo && res.data.logo.length > 0) {
					logo = res.data.logo;
				}

				this.logo = logo;
				this.topNav = this.topNav.concat(res.data.top_nav);
				this.slide = res.data.slide;
				this.iconNav = res.data.icon_nav;
				this.actives = res.data.actives;
				this.hot = res.data.hot;
				this.listMode = res.data.list_mode;
				this.background = (res.data.slide && res.data.slide.length > 0 ? res.data.background : '')

				if (res.data.title && res.data.title.length > 0) {
					getApp().globalData.appName = res.data.title;
					
					// #ifdef MP-BAIDU
					this.bd_title = res.data.title;
					this.bd_description = res.data.description;
					this.bd_keywords = res.data.keywords;
					swan.setPageInfo({
						title: this.bd_title,
						description: this.bd_description,
						keywords: this.bd_keywords,
					});
					// #endif
				}
				
				//插屏广告
				// #ifdef MP-WEIXIN
				if(res.data.wx_ad && wx.createInterstitialAd) {
					setTimeout(() => {
						this.interstitialAd = wx.createInterstitialAd({ adUnitId: res.data.wx_ad })
						this.interstitialAd.onLoad(() => {
							// console.log('onLoad event emit')
							this.interstitialAd.show().catch((err) => {
								console.error(err)
							})
						})
						this.interstitialAd.onError((err) => {
							// console.log('onError event emit', err)
						})
						this.interstitialAd.onClose((res) => {
							// console.log('onClose event emit', res)
						})
					}, 1000 * parseInt(res.data.wx_ad_delay))
				}
				// #endif
			}); //加载文章

			this.loadPostLast(true);
		},
		
		onShow() {
			// #ifdef MP-BAIDU
			if (this.bd_title) {
				swan.setPageInfo({
					title: this.bd_title,
					description: this.bd_description,
					keywords: this.bd_keywords,
				});
			}
			// #endif
		},

		onReachBottom() {
			if (this.currentTab == 0) {
				if (!this.pullUpOnLast) {
					return;
				}

				this.loadPostLast(false);
			} else {
				if (!this.pullUpOn) {
					return;
				}

				this.loadPost(false);
			}
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

		computed: {
			StatusBar() {

			},
		},

		methods: {
			//nav start----
			handlerSearchClick(e) {
				uni.navigateTo({
					url: '/pages/search/search'
				});
			},
			//nav end ----

			//slide start----
			handlerSlideChange(e) {
				this.current = e.detail.current;
			},
			//slide end----

			//tab -- start
			swichNav(e) {
				let cur = e.currentTarget.dataset.current;
				if (this.currentTab == cur) {
					return false;
				}
				
				this.background = (cur == 0 && this.slide && this.slide.length > 0 ? Api.JIANGQIE_BG_INDEX : '');
				this.currentTab = cur;

				if (cur !== 0) {
					this.loadPost(true);
				}
			},

			handlerTabMoreClick(e) {
				uni.switchTab({
					url: '/pages/categories/categories'
				});
			},
			//tab -- end

			handlerIconNavClick(e) {
				let link = e.currentTarget.dataset.link;
				Util.openLink(link);
			},

			handlerActiveClick(e) {
				let link = e.currentTarget.dataset.link;
				Util.openLink(link);
			},

			handlerArticleClick(e) {
				let post_id = e.currentTarget.dataset.id;
				uni.navigateTo({
					url: '/pages/article/article?post_id=' + post_id
				});
			},

			//加载数据
			loadPostLast(refresh) {
				this.loaddingLast = true;
				let offset = 0;

				if (!refresh) {
					offset = this.postsLast.length;
				}

				Rest.get(Api.JIANGQIE_POSTS_LAST, {
					'offset': offset
				}).then(res => {
					this.loaddingLast = false;
					this.postsLast = (refresh ? res.data : this.postsLast.concat(res.data));
					this.pullUpOnLast = (res.data.length >= Constants.JQ_PER_PAGE_COUNT)
				});
			},

			loadPost(refresh) {
				this.loadding = true;
				let offset = 0;
				if (!refresh) {
					offset = this.posts.length;
				}

				Rest.get(Api.JIANGQIE_POSTS_CATEGORY, {
					'offset': offset,
					'cat_id': this.topNav[this.currentTab].id
				}).then(res => {
					this.loadding = false;
					this.posts = (refresh ? res.data : this.posts.concat(res.data));
					this.pullUpOn = res.data.length >= Constants.JQ_PER_PAGE_COUNT
				});
			},
		}
	};
</script>

<style lang="scss" scoped>
	.jiangqie-logo {
		display: flex;
		align-items: center;
		margin-right: 15rpx;

		image {
			height: 48rpx;
			width: 128rpx;
		}
	}

	.jiangqie-btn-search {
		display: flex;
		align-items: center;
		width: 80%;
		height: 32px;
		padding-left: 20rpx;
		background: #d5544f;
		border-radius: 16px;
		color: #f8b8b8;
		font-size: 28rpx;
	}

	.container {
		display: flex;
		flex-direction: column;
		box-sizing: border-box;
		padding-bottom: env(safe-area-inset-bottom);
		background: #FFF;
	}

	.jiangqie-top-bg {
		background-repeat: no-repeat;
		background-size: 100% auto;
		background-position-y: 100rpx;
	}

	/*banner*/

	.jiangqie-banner-box {
		width: 100%;
	}

	.jiangqie-banner-swiper {
		width: 100%;
		height: 320rpx;
	}

	.jiangqie-banner-item {
		padding: 0 16rpx;
		box-sizing: border-box;
	}

	.jiangqie-slide-image {
		width: 100%;
		height: 320rpx;
		display: block;
		border-radius: 20rpx;
		transition: all 0.1s linear;
		box-shadow: 3rpx 3rpx 10rpx rgba(0, 0, 0, 0.2);
	}

	.jiangqie-banner-scale {
		transform: scaleY(0.9);
		transform-origin: center center;
	}

	.jiangqie-banner-swiper .wx-swiper-dot {
		width: 8rpx;
		height: 8rpx;
		display: inline-flex;
		background: none;
		justify-content: space-between;
	}

	.jiangqie-banner-swiper .wx-swiper-dot::before {
		content: '';
		flex-grow: 1;
		background: rgba(255, 255, 255, 0.3);
		border-radius: 16rpx;
		overflow: hidden;
	}

	.jiangqie-banner-swiper .wx-swiper-dot-active::before {
		background: #FFF;
	}

	.jiangqie-banner-swiper .wx-swiper-dot.wx-swiper-dot-active {
		width: 16rpx;
	}


	/** jiangqie-news-icon **/
	.jiangqie-news-icon {
		box-sizing: border-box;
		display: flex;
		align-items: center;
		justify-content: space-between;
		flex-wrap: wrap;
		font-size: 24rpx;
		color: #555;
		margin-top: 10rpx;
	}

	.jiangqie-news-iconbox {
		width: 25%;
		display: flex;
		align-items: center;
		justify-content: space-between;
		flex-direction: column;
		margin-bottom: 10rpx;
	}

	.jiangqie-news-iconimg {
		height: 100rpx;
		width: 100rpx;
		border-radius: 16rpx;
	}

	.jiangqie-news-iconbox text {
		line-height: 46rpx;
	}

	/** jiangqie-news-lightbox **/
	.jiangqie-news-view {
		overflow: hidden;
		padding: 0 30rpx;
	}

	.jiangqie-news-lightbox {
		margin-bottom: 20rpx;
		float: left;
		position: relative;
		border-radius: 20rpx;
	}

	.jiangqie-news-lightbox-main {
		height: 300rpx;
		width: 320rpx;
		// background: linear-gradient(to right bottom, #7FD3FF, #2BB1F2);
		box-shadow: 5rpx 5rpx 20rpx rgba(0, 0, 0, 0.2);
	}

	.jiangqie-news-lightbox-side,
	.jiangqie-news-lightbox-side2 {
		height: 141rpx;
		width: 350rpx;
		text-align: left;
		margin-left: 20rpx;
		// background: linear-gradient(to right bottom, #9788FF, #735CFF);
		box-shadow: 5rpx 5rpx 20rpx rgba(0, 0, 0, 0.2);
	}

	.jiangqie-news-lightbox-side2 {
		// background: linear-gradient(to right bottom, #FF91C2, #FF539E);
	}

	.jiangqie-news-lightimg {
		height: 300rpx;
		width: 320rpx;
		border-radius: 20rpx;
	}

	.jiangqie-news-lightbox-side .jiangqie-news-lightimg,
	.jiangqie-news-lightbox-side2 .jiangqie-news-lightimg {
		height: 141rpx;
		width: 350rpx;
		position: relative;
	}


	.jiangqie-news-lighttext {
		position: absolute;
		left: 0rpx;
		bottom: 0rpx;
		text-align: left;
		background: rgba(0, 0, 0, 0.3);
		overflow: hidden;
		z-index: 9;
		width: 280rpx;
		border-radius: 0 0 20rpx 20rpx;
		height: 60rpx;
		padding: 0 20rpx;
	}

	.jiangqie-news-lightbox-side .jiangqie-news-lighttext,
	.jiangqie-news-lightbox-side2 .jiangqie-news-lighttext {
		width: 310rpx;
	}

	.jiangqie-news-lihgttitle {
		color: #FFF;
		font-size: 26rpx;
		font-weight: 300;
		line-height: 70rpx;
	}


	/** jiangqie-news-block **/

	.jiangqie-news-type {
		font-size: 36rpx;
		font-weight: 500;
		color: #555;
		padding: 16rpx 0 0;
	}

	.jiangqie-news-type text {
		float: right;
		font-size: 24rpx;
		font-weight: 200;
		color: #999;
		margin-right: 30rpx;
	}

	.jiangqie-news-block {
		position: relative;
		padding: 30rpx 0;
		min-height: 160rpx;
		border-bottom: 1rpx solid #DDD;
		padding-right: 260rpx;
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
		font-size: 26rpx;
		color: #999;
		line-height: 30rpx;
		margin-bottom: 18rpx;
	}

	.jiangqie-news-info {
		font-size: 22rpx;
		color: #BBB;
		vertical-align: bottom;
		height: 26rpx;
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
		display: none;
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

	.jiangqie-news-hot {
		padding: 10rpx 0;
		border-bottom: 12rpx solid #F3F3F3;
		border-top: 12rpx solid #F3F3F3;
	}

	.jiangqie-news-hot .jiangqie-news-type {
		padding-left: 30rpx;
		padding-bottom: 20rpx;
	}

	.jiangqie-news-scroll-x-box {
		height: 290rpx;
		white-space: nowrap;
	}

	::-webkit-scrollbar {
		width: 0;
		height: 0;
		color: transparent;
	}

	.jiangqie-hot-scroll-box {
		width: 440rpx;
		height: 290rpx;
		margin-left: 20rpx;
		display: inline-block;
	}

	.jiangqie-hot-scroll-box:first-child {
		margin-left: 30rpx;
	}

	.jiangqie-hot-scroll-box:last-child {
		margin-right: 30rpx;
	}

	.jiangqie-hot-scroll-image {
		width: 440rpx;
		height: 220rpx;
		border-radius: 16rpx;
		box-shadow: 5rpx 5rpx 20rpx rgba(0, 0, 0, 0.2);
		margin-bottom: 10rpx;
	}

	.jiangqie-hot-scroll-title {
		font-size: 32rpx;
		color: #333;
		font-weight: 400;
		line-height: 36rpx;
		height: 36rpx;
		white-space: normal;
		overflow: hidden;
	}

	.tab-more {
		position: fixed;
		right: 0;
		height: 100rpx;
		width: 120rpx;
		z-index: 99;
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
		width: 100%;
		height: 100rpx;
		overflow: hidden;
		box-sizing: border-box;
		position: fixed;
		left: 0;
		z-index: 99;
		background: #E0301F;
		white-space: nowrap;
	}

	.tab-bar-item {
		padding: 0;
		height: 100rpx;
		min-width: 80rpx;
		line-height: 100rpx;
		margin: 0 28rpx;
		display: inline-block;
		text-align: center;
		box-sizing: border-box;
	}

	.tab-bar-title {
		height: 100rpx;
		line-height: 100rpx;
		font-size: 28rpx;
		color: rgb(248, 184, 184);
		font-weight: 300;
	}

	.active {
		border-bottom: 6rpx solid #FFF;
	}

	.active .tab-bar-title {
		color: #FFF !important;
		font-size: 32rpx;
		font-weight: bold;
	}

	/*tabbar end*/

	.jiangqie-news-title text {
		font-size: 22rpx;
		font-weight: 300;
		color: #FFF;
		background: #F30;
		float: left;
		width: 60rpx;
		height: 36rpx;
		margin-top: 6rpx;
		line-height: 36rpx;
		text-align: center;
		border-radius: 8rpx;
		margin-right: 12rpx;
	}
</style>
