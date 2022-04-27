<template>
	<view>
		<view :style="background?'background-image: url(' + background + ');':''"
			style="background: no-repeat;background-size: 100% auto;">
			<view class="jiangqie-login">
				<view class="jiangqie-app-info">
					<!--替换为小程序logo-->
					<image src="/static/images/default_avatar.jpg" mode="aspectFill"></image>
					<view class="jiangqie-app-name">{{title}}</view>
					<view class="jiangqie-app-slogan">授权登录可查看更多内容</view>
				</view>
				<view class="jiangqie-login-btn">
					<button @tap.stop="handlerCancelClick" class="jiangqie-login-btnl">取消</button>

					<!-- #ifdef MP-WEIXIN -->
					<button v-if="code" @tap.stop="clickLogin" class="jiangqie-login-btnr">确定</button>
					<template v-else>
						<button class="jiangqie-login-btnl">确定</button>
						<view class="jiangqie-no-login-tip">
							<view>请在后台配置微信Appid和微信AppSecret</view>
						</view>
					</template>
					<!-- #endif -->

					<!-- #ifdef MP-QQ -->
					<button v-if="code" open-type="getUserInfo" class="jiangqie-login-btnr"
						@getuserinfo="getuserinfo">确定</button>
					<template v-else>
						<button class="jiangqie-login-btnl">确定</button>
						<view class="jiangqie-no-login-tip">
							<view>请在后台配置QQAppid和QQAppSecret</view>
						</view>
					</template>
					<!-- #endif -->

					<!-- #ifdef MP-BAIDU -->
					<button v-if="code" open-type="getUserInfo" class="jiangqie-login-btnr"
						@getuserinfo="getuserinfo">确定</button>
					<template v-else>
						<button class="jiangqie-login-btnl">确定</button>
						<view class="jiangqie-no-login-tip">
							<view>请在后台配置百度AppKey和百度AppSecret</view>
						</view>
					</template>
					<!-- #endif -->

					<!-- #ifdef H5 -->
					<button @tap.stop="clickLoginTest" class="jiangqie-login-btnr">确定</button>
					<view class="jiangqie-no-login-tip">
						<view>微信/百度/QQ已接入</view>
						<view>当前平台尚未接入账号体系</view>
						<view>登录仅为演示</view>
					</view>
					<!-- #endif -->
				</view>
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
	const Util = require("@/utils/util.js");
	const Auth = require("@/utils/auth.js");
	const Api = require("@/utils/api.js");
	const Rest = require("@/utils/rest.js");
	import JiangqieLoading from "@/components/loading/loading";

	export default {
		data() {
			return {
				background: '',
				title: '',
				loading: false,

				code: undefined,
			};
		},

		components: {
			JiangqieLoading
		},

		props: {},

		onLoad(options) {
			this.title = getApp().globalData.appName;

			// #ifdef MP-WEIXIN || MP-QQ
			uni.login({
				success: (res) => {
					this.code = res.code;
				}
			});
			// #endif

			// #ifdef MP-BAIDU
			swan.login({
				success: res => {
					swan.getLoginCode({
						success: (res2) => {
							this.code = res2.code;
						}
					})
				},
				fail: err => {
					console.log(err)
					swan.showToast({
						title: '登录失败',
						icon: 'none'
					});
				}
			});
			// #endif

			Rest.get(Api.JIANGQIE_SETTING_LOGIN).then(res => {
				this.background = res.data.background;
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
			handlerCancelClick(e) {
				Util.navigateBack();
			},

			clickLoginTest(e) {
				Rest.get(Api.JIANGQIE_USER_LOGIN_TEST, {}).then(res => {
					Auth.setUser(res.data);
					Util.navigateBack();
				}, err => {
					console.log(err)
				});
			},

			clickLogin(e) {
				wx.getUserProfile({
					desc: '用于完善会员资料',
					success: res => {
						// console.log(res);

						let userInfo = res.userInfo;
						this.login(userInfo.nickName, userInfo.avatarUrl);
					},
					fail: (err) => {
						console.log(err);
					}
				})
			},

			getuserinfo(res) {
				let userInfo = res.detail.userInfo;
				this.login(userInfo.nickName, userInfo.avatarUrl);
			},

			login(nickname, avatar) {
				let params = {
					code: this.code,
					nickname: nickname,
					avatar: avatar
				};

				// #ifdef MP-WEIXIN
				params.channel = 'weixin';
				// #endif

				// #ifdef MP-QQ
				params.channel = 'qq';
				// #endif

				// #ifdef MP-BAIDU
				params.channel = 'baidu';
				// #endif

				Rest.get(Api.JIANGQIE_USER_LOGIN, params).then(res => {
					Auth.setUser(res.data);
					Util.navigateBack();
				}, err => {
					console.log(err)
				});
			}
		}
	};
</script>

<style lang="scss" scoped>
	.jiangqie-app-info {
		padding: 50rpx 40rpx;
		text-align: center;
	}

	.jiangqie-app-info image {
		height: 200rpx;
		width: 200rpx;
		border-radius: 200rpx;
	}

	.jiangqie-app-name {
		font-size: 32rpx;
		color: #FFF;
		font-weight: 500;
		line-height: 80rpx;
	}

	.jiangqie-app-slogan {
		font-size: 28rpx;
		color: rgb(248, 184, 184);
		font-weight: 400;
		line-height: 40rpx;
	}

	.jiangqie-login-btn {
		padding: 30rpx 160rpx;
	}


	.jiangqie-login-btnl,
	.jiangqie-login-btnr {
		height: 80rpx;
		font-size: 30rpx;
		color: #555;
		font-weight: 400;
		border-radius: 16rpx;
		border: 2rpx solid #DDD;
		margin-top: 30rpx;
	}

	.jiangqie-login-btnl {
		color: #999;
		background: #FFF;
	}

	.jiangqie-no-login-tip {
		margin-top: 30rpx;
		text-align: center;
		font-size: 24rpx;
		color: #999999;
	}
</style>
