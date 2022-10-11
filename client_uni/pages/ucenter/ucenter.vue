<template>
	<view v-if="setting" class="jiangqie-top-bg" :style="'background-image: url(' + setting.background + ');'">
		<view class="jiangqie-userbox">
			<view class="jiangqie-usersetup">
				<image src="/static/images/setup.png"></image>
			</view>
			<view class="jiangqie-userinfo">
				<template v-if="user">
					<view class="jiangqie-useravatar">
						<image :src="user.avatar" mode="aspectFill"></image>
					</view>
					<view class="jiangqie-username">
						<text>{{user.nickname}}</text>
					</view>
				</template>
				<template v-else>
					<view class="jiangqie-useravatar" @click="clickLogin">
						<image src="/static/images/default_avatar.jpg" mode="aspectFill"></image>
						<view class="jiangqie-useralogin">
							<view>立即登录</view>
						</view>
					</view>
				</template>
			</view>
		</view>
		<view class="jiangqie-listbox jiangqie-postcount">
			<template v-for="(item, index) in menu">
				<view v-if="item.tp=='views'" :key="index"
					:class="'jiangqie-listblock ' + (item.line=='yes'?'jiangqie-listblock-line':'')" data-track="views"
					@tap.stop="handlerPostTrack">
					<view class="jiangqie-listcont">
						<image src="/static/images/more.png" mode="aspectFill"></image>
					</view>
					<view class="jiangqie-listtitle">
						<text>{{item.title}}</text>
						<image v-if="item.icon" :src="item.icon" mode="aspectFill"></image>
					</view>
				</view>
				<view v-if="item.tp=='likes'" :key="index"
					:class="'jiangqie-listblock ' + (item.line=='yes'?'jiangqie-listblock-line':'')" data-track="likes"
					@tap.stop="handlerPostTrack">
					<view class="jiangqie-listcont">
						<image src="/static/images/more.png" mode="aspectFill"></image>
					</view>
					<view class="jiangqie-listtitle">
						<text>{{item.title}}</text>
						<image v-if="item.icon" :src="item.icon" mode="aspectFill"></image>
					</view>
				</view>
				<view v-if="item.tp=='favorites'" :key="index"
					:class="'jiangqie-listblock ' + (item.line=='yes'?'jiangqie-listblock-line':'')"
					data-track="favorites" @tap.stop="handlerPostTrack">
					<view class="jiangqie-listcont">
						<image src="/static/images/more.png" mode="aspectFill"></image>
					</view>
					<view class="jiangqie-listtitle">
						<text>{{item.title}}</text>
						<image v-if="item.icon" :src="item.icon" mode="aspectFill"></image>
					</view>
				</view>
				<view v-if="item.tp=='comments'" :key="index"
					:class="'jiangqie-listblock ' + (item.line=='yes'?'jiangqie-listblock-line':'')"
					data-track="comments" @tap.stop="handlerPostTrack">
					<view class="jiangqie-listcont">
						<image src="/static/images/more.png" mode="aspectFill"></image>
					</view>
					<view class="jiangqie-listtitle">
						<text>{{item.title}}</text>
						<image v-if="item.icon" :src="item.icon" mode="aspectFill"></image>
					</view>
				</view>
				<view v-if="item.tp=='about'" :key="index"
					:class="'jiangqie-listblock ' + (item.line=='yes'?'jiangqie-listblock-line':'')"
					@tap.stop="handlerAbout">
					<view class="jiangqie-listcont">
						<image src="/static/images/more.png" mode="aspectFill"></image>
					</view>
					<view class="jiangqie-listtitle">
						<text>{{item.title}}</text>
						<image v-if="item.icon" :src="item.icon" mode="aspectFill"></image>
					</view>
				</view>
				<view v-if="item.tp=='feedback'" :key="index"
					:class="'jiangqie-listblock ' + (item.line=='yes'?'jiangqie-listblock-line':'')">
					<view class="jiangqie-listcont">
						<image src="/static/images/more.png" mode="aspectFill"></image>
					</view>
					<button open-type="feedback" class="jiangqie-listtitle">
						<text>{{item.title}}</text>
						<image v-if="item.icon" :src="item.icon" mode="aspectFill"></image>
					</button>
				</view>
				<view v-if="item.tp=='contact'" :key="index"
					:class="'jiangqie-listblock ' + (item.line=='yes'?'jiangqie-listblock-line':'')">
					<view class="jiangqie-listcont">
						<image src="/static/images/more.png" mode="aspectFill"></image>
					</view>
					<button open-type="contact" class="jiangqie-listtitle">
						<text>{{item.title}}</text>
						<image v-if="item.icon" :src="item.icon" mode="aspectFill"></image>
					</button>
				</view>
				<view v-if="item.tp=='clear'" :key="index"
					:class="'jiangqie-listblock ' + (item.line=='yes'?'jiangqie-listblock-line':'')"
					@tap.stop="handlerClearCache">
					<view class="jiangqie-listcont">
						<image src="/static/images/more.png" mode="aspectFill"></image>
					</view>
					<view class="jiangqie-listtitle">
						<text>{{item.title}}</text>
						<image v-if="item.icon" :src="item.icon" mode="aspectFill"></image>
					</view>
				</view>
				<view v-if="item.tp=='page'" :key="index"
					:class="'jiangqie-listblock ' + (item.line=='yes'?'jiangqie-listblock-line':'')"
					:data-page_id="item.page_id" @tap.stop="handlerPageClick">
					<view class="jiangqie-listcont">
						<image src="/static/images/more.png" mode="aspectFill"></image>
					</view>
					<view class="jiangqie-listtitle">
						<text>{{item.title}}</text>
						<image v-if="item.icon" :src="item.icon" mode="aspectFill"></image>
					</view>
				</view>
				<view v-if="item.tp=='link'" :key="index"
					:class="'jiangqie-listblock ' + (item.line=='yes'?'jiangqie-listblock-line':'')"
					:data-link="item.link" @tap.stop="handlerLinkClick">
					<view class="jiangqie-listcont">
						<image src="/static/images/more.png" mode="aspectFill"></image>
					</view>
					<view class="jiangqie-listtitle">
						<text>{{item.title}}</text>
						<image v-if="item.icon" :src="item.icon" mode="aspectFill"></image>
					</view>
				</view>
				<view v-else-if="item.tp=='split'" :key="index" class="jiangqie-list-split"></view>
			</template>
		</view>

		<view class="jiangqie-nomore">
			<view @click="clickJiangQie" class="jiangqie-text">© 追格 zhuige.com</view>
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
	 * Copyright © 2020-2022 www.zhuige.com All rights reserved.
	 */
	const Auth = require("@/utils/auth.js");
	const Api = require("@/utils/api.js");
	const Util = require("@/utils/util.js");
	const Rest = require("@/utils/rest.js");

	export default {
		data() {
			this.default = {
				menu: [ //views,likes,favorites,comments,about,feedback,contact,clear,split,link,page
					{
						tp: 'views',
						icon: "/static/images/icon_view.png",
						title: '我的浏览',
						line: 1
					}, {
						tp: 'likes',
						icon: "/static/images/icon_like.png",
						title: '我的点赞',
						line: 1
					}, {
						tp: 'favorites',
						icon: "/static/images/icon_fav.png",
						title: '我的收藏',
						line: 1
					}, {
						tp: 'comments',
						icon: "/static/images/icon_com.png",
						title: '我的评论',
						line: 0
					}, {
						tp: 'split'
					}, {
						tp: 'about',
						icon: '',
						title: '关于我们',
						line: 1
					}, {
						tp: 'feedback',
						icon: '',
						title: '意见反馈',
						line: 1
					}, {
						tp: 'contact',
						icon: '',
						title: '在线客服',
						line: 1
					}, {
						tp: 'clear',
						icon: '',
						title: '清除缓存',
						line: 0
					}
				]
			};

			return {
				setting: undefined,
				user: undefined,
				menu: {},
			};
		},

		onShow(options) {
			this.user = Auth.getUser();
			Rest.get(Api.JIANGQIE_SETTING_UCENTER).then(res => {
				let menu = this.default.menu;
				if (res.data.menu.length > 0) {
					menu = res.data.menu;
				}

				this.setting = {
					background: res.data.background
				};
				this.menu = menu;
			});
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
			clickLogin() {
				Util.openLink('/pages/login/login')
			},
			
			handlerPostTrack(e) {
				if (!Auth.getUser()) {
					uni.navigateTo({
						url: '/pages/login/login'
					})
					return;
				}

				let track = e.currentTarget.dataset.track;
				uni.navigateTo({
					url: '/pages/list/list?track=' + track
				});
			},

			handlerAbout(e) {
				uni.navigateTo({
					url: '/pages/about/about'
				});
			},

			handlerClearCache(e) {
				uni.showModal({
					title: '提示',
					content: '清除缓存 需要重新登录',
					success: (res) => {
						if (res.confirm) {
							uni.clearStorageSync();
							uni.showToast({
								icon: 'none',
								title: '清除完毕'
							});
							uni.reLaunch({
								url: '/pages/index/index'
							});
						}
					}

				});
			},

			handlerLinkClick(e) {
				let link = e.currentTarget.dataset.link;

				if (link.startsWith('/pages')) {
					uni.navigateTo({
						url: link
					});
				} else {
					uni.navigateToMiniProgram({
						appId: link,
						fail: res => {
							if (res.errMsg && res.errMsg.indexOf('fail cancel') < 0) {
								uni.showToast({
									icon: 'none',
									title: res.errMsg
								});
							}
						}
					});
				}
			},

			handlerPageClick(e) {
				let page_id = e.currentTarget.dataset.page_id;
				uni.navigateTo({
					url: '/pages/viewhtml/viewhtml?page_id=' + page_id
				});
			},

			clickJiangQie() {
				Util.jiangqie();
			}
		}
	};
</script>

<style lang="scss" scoped>
	.jiangqie-top-bg {
		background-repeat: no-repeat;
		background-size: 750rpx 520rpx;
		background-position: 0 -120rpx;
	}

	.jiangqie-userbox {
		padding-bottom: 30rpx;
		min-height: 410rpx;
	}

	.jiangqie-usersetup {
		display: none;
		padding-top: 40rpx;
		height: 40rpx;
		line-height: 40rpx;
	}

	.jiangqie-usersetup image {
		float: right;
		height: 36rpx;
		width: 36rpx;
		margin-right: 40rpx;
	}

	.jiangqie-userinfo {
		padding-bottom: 20rpx;
		text-align: center;
	}

	.jiangqie-useravatar {
		text-align: center;
		height: 240rpx;
		clear: both;
		padding-top: 80rpx;
	}

	.jiangqie-useravatar image {
		height: 152rpx;
		width: 152rpx;
		border-radius: 152rpx;
		border: 12rpx solid #FFF;
		margin-bottom: 20rpx;
	}

	.jiangqie-username {
		height: 50rpx;
		line-height: 50rpx;
		font-size: 36rpx;
		font-weight: 500;
		color: #FFF;
	}

	.jiangqie-useralogin {
		height: 50rpx;
		line-height: 50rpx;
		font-size: 30rpx;
		font-weight: 300;
		color: #FFF;
	}

	.jiangqie-usermember {
		height: 30rpx;
		line-height: 30rpx;
		font-size: 24rpx;
		font-weight: 300;
		color: #9B9B9B;
	}

	.jiangqie-listbox {
		padding: 0;
	}

	.jiangqie-postcount {
		border-bottom: 8rpx solid #F3F3F3;
	}

	.jiangqie-listblock {
		overflow: hidden;
		margin: 0 30rpx;
		padding: 25rpx 0;
	}

	.jiangqie-listblock:last-child {
		border-bottom: none;
	}

	.jiangqie-listcont {
		float: right;
		text-align: right;
	}

	.jiangqie-listcont text {
		font-size: 24rpx;
		font-weight: 200;
		color: #999;
	}

	.jiangqie-listcont image {
		float: right;
		height: 24rpx;
		width: 24rpx;
		margin-left: 10rpx;
		margin-top: 8rpx;
	}

	.jiangqie-listtitle {
		text-align: left;
	}

	.jiangqie-listtitle text {
		font-size: 32rpx;
		font-weight: 400;
		color: #333;
		vertical-align: top;
	}

	.jiangqie-listtitle image {
		float: left;
		height: 40rpx;
		width: 40rpx;
		margin-right: 16rpx;
		margin-top: 2rpx;
	}

	button.jiangqie-listtitle {
		padding: 0;
		line-height: inherit;
		background-color: transparent;
	}

	.jiangqie-listblock-line {
		border-bottom: 1rpx solid #DDD;
	}

	.jiangqie-listblock-line:last-child {
		border-bottom: none;
	}

	.jiangqie-list-split {
		height: 16rpx;
		background-color: #F3F3F3;
	}

	.jiangqie-nomore {
		margin: 50rpx 0;
	}
</style>
