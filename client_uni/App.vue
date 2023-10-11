<script>
	/*
	 * 酱茄小程序开源版 v2.4.0
	 * 作者: 追格
	 * 文档：https://www.zhuige.com/docs/zxfree.html
	 * github: https://github.com/zhuige-com/jiangqie_kafei
	 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
	 * Copyright © 2020-2023 www.zhuige.com All rights reserved.
	 */
	import Vue from 'vue';

	const Auth = require("./utils/auth.js");
	
	export default {
		globalData: {
			appName: '酱茄Free'
		},

		onLaunch() {

		},

		methods: {

		}
	};
</script>
<style>
	page {
		background: #FFFFFF;
	}

	button::after {
		border-radius: 0;
		border: none;
	}

	.jiangqie-loadmore {
		width: 48%;
		margin: 1.5em auto;
		line-height: 1.5em;
		font-size: 24rpx;
		text-align: center;
	}

	.jiangqie-nomore {
		position: relative;
		text-align: center;
		display: flex;
		justify-content: center;
		margin-top: 10rpx;
		padding-bottom: 44rpx;
	}

	.jiangqie-nomore::before {
		content: '';
		position: absolute;
		border-bottom: 1rpx solid #e5e5e5;
		-webkit-transform: scaleY(0.5);
		transform: scaleY(0.5);
		width: 360rpx;
		top: 18rpx;
	}

	.jiangqie-text {
		position: absolute;
		color: #999999;
		font-size: 24rpx;
		text-align: center;
		padding: 0 18rpx;
		height: 36rpx;
		line-height: 36rpx;
		z-index: 1;
	}

	.jiangqie-nomore-dot {
		position: relative;
		text-align: center;
		display: flex;
		justify-content: center;
		margin-top: 10rpx;
		padding-bottom: 40rpx;
	}

	.jiangqie-nomore-dot::before {
		content: '';
		position: absolute;
		border-bottom: 1rpx solid #e5e5e5;
		-webkit-transform: scaleY(0.5);
		transform: scaleY(0.5);
		width: 360rpx;
		top: 18rpx;
	}

	.jiangqie-dot-text {
		position: absolute;
		color: #e5e5e5;
		font-size: 10px;
		text-align: center;
		width: 50rpx;
		height: 36rpx;
		line-height: 36rpx;
		transform: scale(0.8);
		transform-origin: center center;
		z-index: 1;
	}
	/* =========== 账号注销 =========== */
	.jiangqie-wide-box {
		padding: 0 20px 20px;
	}
	
	.jiangqie-logout-info {
		padding: 20px;
		background: #FFFFFF;
		border-radius: 12rpx;
	}
	.jiangqie-logout-info view {
		line-height: 2.2em;
		font-size: 28rpx;
	}
	.jiangqie-logout-info view image {
		width: 100%;
	}
	.jiangqie-logout-info view.jiangqie-logout-title {
		font-size: 33rpx;
		font-weight: 500;
	}
	.jiangqie-logout-form {
		text-align: center;
		padding: 10px;
	}
	.jiangqie-logout-form label {
		font-size: 26rpx;
		font-weight: 300;
	}
	.jiangqie-logout-form view:nth-child(2) {
		width: 60%;
		text-align: center;
		height: 96rpx;
		line-height: 96rpx;
		border-radius: 96rpx;
		font-size: 32rpx;
		font-weight: 400;
		color: #FFFFFF;
		background: #010101;
		margin: 10px auto;
	}
</style>