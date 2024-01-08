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

					<!-- #ifdef MP-BAIDU -->
					<template v-if="is_login_baidu">
						<button v-if="code" @tap.stop="clickLogin" class="jiangqie-login-btnr">确定</button>
						<template v-else>
							<button class="jiangqie-login-btnl">确定</button>
							<view class="jiangqie-no-login-tip">
								<view>请检查后台百度AppKey和AppSecret配置，及manifest.json中百度AppID配置</view>
							</view>
						</template>
					</template>
					<template v-else>
						<button open-type="login" class="jiangqie-login-btnr" @login="baiduAppLogin">点击登录百度App</button>
					</template>
					<!-- #endif -->

					<!-- #ifdef H5 -->
					<button @tap.stop="clickLoginTest" class="jiangqie-login-btnr">确定</button>
					<view class="jiangqie-no-login-tip">
						<view>微信/百度已接入</view>
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

		<view v-if="type!='mobile'" class="jiangqie-login-tip">
			<label @click="clickAgreeLicense">
				<radio :checked="argeeLicense" color="#ff4400" style="transform:scale(0.7)" />
				我已阅读并同意
			</label>
			<text class="link" @click="clickYSTK">《隐私条款》</text>
			<text>及</text>
			<text class="link" @click="clickYHXY">《用户协议》</text>
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

	const Util = require("@/utils/util.js");
	const Auth = require("@/utils/auth.js");
	const Api = require("@/utils/api.js");
	const Rest = require("@/utils/rest.js");

	export default {
		components: {
			
		},
		
		data() {
			return {
				type: 'login',

				background: '',
				title: '',
				
				ystk: undefined,
				yhxy: undefined,

				code: undefined,

				// 是否已登录百度App
				is_login_baidu: false,

				argeeLicense: false,
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

			// #ifdef MP-WEIXIN
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
				if (res.data.ystk) {
					this.ystk = res.data.ystk;
				}
				
				if (res.data.yhxy) {
					this.yhxy = res.data.yhxy;
				}
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
			/**
			 * 点击 打开链接
			 */
			clickLink(link) {
				Util.openLink(link)
			},

			/**
			 * 点击同意协议
			 */
			clickAgreeLicense() {
				this.argeeLicense = !this.argeeLicense;
			},
			
			/**
			 * 点击 隐私条款
			 */
			clickYSTK() {
				if (!this.ystk) {
					Util.toast('请在后台设置隐私条款');
					return;
				}
				Util.openLink('/pages/viewhtml/viewhtml?page_id=' + this.ystk)
			},
			
			/**
			 * 点击 用户协议
			 */
			clickYHXY() {
				if (!this.yhxy) {
					Util.toast('请在后台设置用户协议');
					return;
				}
				Util.openLink('/pages/viewhtml/viewhtml?page_id=' + this.yhxy)
			},

			/**
			 * 点击 取消
			 */
			handlerCancelClick(e) {
				Util.navigateBack();
			},

			/**
			 * 测试 登录功能
			 */
			clickLoginTest(e) {
				if (!this.argeeLicense) {
					Util.toast('请阅读并同意《用户协议》及《隐私条款》');
					return;
				}

				Rest.get(Api.JIANGQIE_USER_LOGIN_TEST, {}).then(res => {
					Auth.setUser(res.data);
					Util.navigateBack();
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 点击 登录
			 */
			clickLogin(e) {
				if (!this.argeeLicense) {
					Util.toast('请阅读并同意《用户协议》及《隐私条款》');
					return;
				}

				// #ifdef MP-WEIXIN
				this.login("微信用户", "");
				// #endif

				// #ifdef MP-BAIDU
				this.login("百度用户", "");
				// #endif
			},

			/**
			 * 百度小程序 登录
			 */
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

			/**
			 * 登录
			 */
			login(nickname, avatar) {
				let params = {
					code: this.code,
					nickname: nickname,
					avatar: avatar
				};

				// #ifdef MP-WEIXIN
				params.channel = 'weixin';
				// #endif

				// #ifdef MP-BAIDU
				params.channel = 'baidu';
				// #endif

				Rest.get(Api.JIANGQIE_USER_LOGIN, params).then(res => {
					Auth.setUser(res.data);
					if (res.data.first) {
						uni.redirectTo({
							url: '/pages/verify/verify'
						})
					} else {
						Util.navigateBack();
					}
				}, err => {
					if (err.msg) {
						uni.showModal({
							content: err.msg
						})
					}
				});
			},

			/**
			 * 获取手机号码
			 */
			getPhoneNumber(e) {
				Rest.post(Api.JIANGQIE_USER_SET_MOBILE, {
					encrypted_data: e.detail.encryptedData,
					iv: e.detail.iv,
					code: this.code,
				}).then(res => {
					Util.toast(res.msg);
					
					Util.navigateBack();
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
		color: #F8B8B8;
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

	.jiangqie-login-tip {
		width: 100%;
		position: fixed;
		bottom: 100rpx;
		line-height: 2rem;
		text-align: center;
		font-size: 22rpx;
		color: #333333;
	}

	.jiangqie-login-tip text.link {
		color: #111111;
		text-decoration: underline;
	}
</style>