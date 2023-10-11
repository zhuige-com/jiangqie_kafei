<template>
	<view class="container">
		<view class="jiangqie-wide-box">
			<view class="jiangqie-logout-info">
				<mp-html :content="explain"></mp-html>
			</view>
			<view class="jiangqie-logout-form">
				<view @click="clickCheckRead">
					<label>
						<radio :checked="checkRead" color="#111111" style="transform:scale(0.7)" />
						我已阅读并知晓了注销申请须知
					</label>
				</view>
				<view @click="clickLogout">立即注销</view>
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
	 * Copyright © 2020-2023 www.zhuige.com All rights reserved.
	 */
	
	const Util = require("@/utils/util.js");
	const Auth = require("@/utils/auth.js");
	const Api = require("@/utils/api.js");
	const Rest = require("@/utils/rest.js");
	
	export default {
		data() {
			return {
				explain: '',
				checkRead: false,
			}
		},
		
		onLoad(options) {
			if (!Auth.getUser()) {
				uni.reLaunch({
					url: '/pages/index/index'
				});
				return;
			}
			
			this.loadSetting()
		},
		
		methods: {
			clickCheckRead() {
				this.checkRead = !this.checkRead;
			},
			
			clickLogout() {
				if (!this.checkRead) {
					Util.toast('阅读并同意注销须知后，才能注销');
					return;
				}
			
				uni.showModal({
					title: '提示',
					content: '确定要注销吗？',
					success: (res) => {
						if (res.cancel) {
							return;
						}
			
						this.logout();
					}
				});
			},
			
			/**
			 * 加载配置
			 */
			loadSetting() {
				Rest.get(Api.JIANGQIE_SETTING_LOGOUT).then(res => {
					this.explain = res.data.explain;
				}, err => {
					console.log(err)
				});
			},
			
			/**
			 * 注销
			 */
			logout() {
				Rest.get(Api.JIANGQIE_USER_LOGOUT).then(res => {
					Util.navigateBack();
				}, err => {
					Util.toast(err.msg);
				});
			}
		}
	}
</script>

<style>

</style>
