<template>
	<view :style="background?'background-image: url(' + background + ');':''"
		style="background: no-repeat;background-size: 100% auto;">
		<view class="jiangqie-login">
			<view class="jiangqie-app-info">
				<!--替换为小程序logo-->
				<image src="/static/images/default_avatar.jpg" mode="aspectFill"></image>
				<view class="jiangqie-app-name">{{title}}</view>
				<view class="jiangqie-app-slogan">
					{{type=='login' ? '授权登录可查看更多内容' : '绑定手机号后才能评论'}}
				</view>
			</view>
			<view class="jiangqie-login-btn">
				<button @tap.stop="handlerCancelClick" class="jiangqie-login-btnl">取消</button>

				<template v-if="type=='login'">
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
					<template v-if="is_login_baidu">
						<button v-if="code" open-type="getUserInfo" class="jiangqie-login-btnr"
							@getuserinfo="getuserinfo">确定</button>
						<template v-else>
							<button class="jiangqie-login-btnl">确定</button>
							<view class="jiangqie-no-login-tip">
								<view>请检查后台百度AppKey和AppSecret配置，及manifest.json中百度AppID配置</view>
							</view>
						</template>
					</template>
					<template v-else>
						<button open-type="login" class="jiangqie-login-btnr"
							@login="baiduAppLogin">点击登录百度App</button>
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
				</template>
				<template v-if="type=='mobile'">
					<!-- #ifdef MP-WEIXIN -->
					<button type="default" class="jiangqie-login-btnl" open-type="getPhoneNumber"
						@getphonenumber="getPhoneNumber">绑定手机号</button>
					<!-- #endif -->
					
					<!-- #ifndef MP-WEIXIN -->
					该平台下的手机绑定功能暂未实现
					<!-- #endif -->
				</template>
			</view>
		</view>
		
		<view class="bottom-copyright">
			<view @click="clickLink('/pages/viewhtml/viewhtml?page_id=1192')">《免责声明》</view>
			<view @click="clickLink('/pages/viewhtml/viewhtml?page_id=251')">《关于我们》</view>
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
	const Util = require("@/utils/util.js");
	const Auth = require("@/utils/auth.js");
	const Api = require("@/utils/api.js");
	const Rest = require("@/utils/rest.js");

	export default {
		data() {
			return {
				type: 'login',

				background: '',
				title: '',

				code: undefined,
				
				// 会否已登录百度App
				is_login_baidu: false,
			};
		},

		onLoad(options) {
			if (options.type) {
				this.type = options.type;
			}
			
			let nav_title = (this.type == 'login' ? '登录' : '绑定手机号');
			uni.setNavigationBarTitle({
				title: nav_title
			})

			this.title = getApp().globalData.appName;

			// #ifdef MP-WEIXIN || MP-QQ
			uni.login({
				success: (res) => {
					this.code = res.code;
				}
			});
			// #endif
			
			// #ifdef MP-BAIDU
			this.is_login_baidu = swan.isLoginSync().isLogin;
			if (this.is_login_baidu) {
				swan.getLoginCode({
					success: res => {
						this.code = res.code;
					}
				});
			}
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
			clickLink(link) {
				Util.openLink(link)
			},
			
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
						let userInfo = res.userInfo;
						this.login(userInfo.nickName, userInfo.avatarUrl);
					},
					fail: (err) => {
						console.log(err);
					}
				})
			},
			
			baiduAppLogin(e) {
				if (!e.detail.errCode) {
					this.is_login_baidu = true;
					swan.getLoginCode({
						success: res => {
							this.code = res.code;
						}
					});
				}
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
					// console.log(err)
					if (err.msg) {
						uni.showToast({
							icon: 'none',
							title: err.msg
						});
					}
				});
			},
			
			getPhoneNumber(e) {
				Rest.post(Api.JIANGQIE_USER_SET_MOBILE, {
					encrypted_data: e.detail.encryptedData,
					iv: e.detail.iv,
					code: this.code,
				}).then(res => {
					uni.showToast({
						icon: 'none',
						title: res.msg
					});
					Util.navigateBack();
				}, err => {
					uni.showToast({
						icon: 'none',
						title: err.msg
					});
				})
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
		margin-top: 160rpx;
		padding: 0rpx 160rpx;
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
	
	.bottom-copyright {
		width: 100%;
		position: fixed;
		bottom: 100rpx;
		color: blue;
		line-height: 2rem;
		text-align: center;
	}
</style>
