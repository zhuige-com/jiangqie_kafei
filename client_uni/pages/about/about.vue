<template>
	<view class="container">
		<view class="jiangqie-flex">
			<view class="jiangqie-cube-box" @tap="tipClick">
				<view class="jiangqie-cube jiangqie-cube-1">❤</view>
				<view class="jiangqie-cube jiangqie-cube-2">V{{version}}</view>
				<view class="jiangqie-cube jiangqie-cube-3">酱茄</view>
				<view class="jiangqie-cube jiangqie-cube-4">开源</view>
				<view class="jiangqie-cube jiangqie-cube-5">小程序</view>
				<view class="jiangqie-cube jiangqie-cube-6">免费</view>
			</view>
		</view>
		<view class="jiangqie-content-box">
			<view class="jiangqie-content">酱茄小程序开源版，基于WordPress开源程序和WordPress REST
				API开发，实现WordPress网站数据与小程序数据同步共享，通过简单的配置就能搭建自己的小程序。项目免费开源，源码可在
				<text class="jiangqie-content-email" @tap="copy"
					data-text="https://github.com/zhuige-com/jiangqie_kafei">GitHub</text>上下载，欢迎Watch & Star ★。
			</view>
			<view class="jiangqie-content">项目可能存在缺陷或者bug，如果您在使用过程中发现问题或者有更好的建议，可反馈给我们。</view>
			<view class="jiangqie-content jiangqie-content-email" @tap="copy" data-text="www.zhuige.com">
				技术支持：www.zhuige.com</view>
			<view class="jiangqie-footer">
				<image src="/static/images/qrcode.jpg" class="jiangqie-applets" mode="widthFix"></image>
				<view class="jiangqie-footer-text">扫描二维码，体验酱茄开源小程序！</view>
			</view>
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
	const Constant = require("@/utils/constants.js");
	const Util = require("@/utils/util.js");

	export default {
		components: {
			
		},
		
		data() {
			return {
				version: Constant.JQ_VERSION,
			};
		},

		onLoad(options) {},

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
			 * 点击复制网址
			 */
			copy(e) {
				let text = e.currentTarget.dataset.text;
				uni.setClipboardData({
					data: text,
					success: (res) => {
						uni.getClipboardData({
							success: (res) => {
								Util.toast('网址已复制');
							}
						});
					},
					fail: (res) => {
						if (res.errMsg && res.errMsg.indexOf('cancel') < 0) {
							Util.toast(res.errMsg);
						}
					}
				});
			},

			/**
			 * 点击 提示
			 */
			tipClick() {
				Util.toast('酱茄小程序开源版');
			}
		}
	};
</script>

<style lang="scss" scoped>
	.container {
		padding: 0 50rpx 40rpx 50rpx;
		box-sizing: border-box;
		overflow: hidden;
	}

	.jiangqie-flex {
		display: flex;
		align-items: center;
		justify-content: center;
		padding-top: 40px;
	}

	.jiangqie-cube-box {
		width: 60px;
		height: 60px;
		position: relative;
		-webkit-transform-style: preserve-3d;
		transform-style: preserve-3d;
		animation: jiangqie-go 4s linear infinite;
	}

	@keyframes jiangqie-go {
		0% {
			-webkit-transform: rotateX(0) rotateY(0);
			transform: rotateX(0) rotateY(0);
		}

		100% {
			-webkit-transform: rotateX(360deg) rotateY(360deg);
			transform: rotateX(360deg) rotateY(360deg);
		}
	}

	.jiangqie-cube {
		width: 60px;
		height: 60px;
		position: absolute;
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 5px;
		box-sizing: border-box;
		font-size: 24rpx;
		color: #fff;
		text-align: center;
		word-break: break-all;
		word-wrap: break-word;
	}

	.jiangqie-cube-1 {
		background: #fff;
		-webkit-transform: rotateY(90deg) translateZ(-30px);
		transform: rotateY(90deg) translateZ(-30px);
		color: #333 !important;
	}

	.jiangqie-cube-2 {
		background: #8a5966;
		-webkit-transform: rotateY(90deg) translateZ(30px);
		transform: rotateY(90deg) translateZ(30px);
	}

	.jiangqie-cube-3 {
		background: linear-gradient(-90deg, #5677fc, #5c8dff);
		-webkit-transform: rotateX(90deg) translateZ(30px);
		transform: rotateX(90deg) translateZ(30px);
	}

	.jiangqie-cube-4 {
		background: #ed3f14;
		-webkit-transform: rotateX(90deg) translateZ(-30px);
		transform: rotateX(90deg) translateZ(-30px);
	}

	.jiangqie-cube-5 {
		background: #ff7900;
		-webkit-transform: translateZ(30px);
		transform: translateZ(30px);
	}

	.jiangqie-cube-6 {
		background: #19be6b;
		-webkit-transform: translateZ(-30px);
		transform: translateZ(-30px);
	}

	.jiangqie-content-box {
		width: 100%;
		margin-top: 50px;
		padding: 30rpx;
		box-sizing: border-box;
		background: #fff;
		border-radius: 10rpx;
		box-shadow: 0 0 10rpx #eee;
	}

	.jiangqie-content {
		color: #333;
		font-size: 30rpx;
		line-height: 44rpx;
		padding: 10rpx 0;
		text-align: justify;
	}

	.jiangqie-content-email {
		color: #5677fc !important;
	}

	.jiangqie-footer {
		display: flex;
		align-items: center;
		padding-top: 30rpx;
	}

	.jiangqie-applets {
		width: 80px;
		height: 80px;
		display: block;
	}

	.jiangqie-footer-text {
		font-size: 24rpx;
		color: #999;
		padding-left: 20rpx;
		width: 300rpx;
	}
</style>