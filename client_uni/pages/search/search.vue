<template>
	<view class="container">
		<view class="jiangqie-searchbox">
			<view class="jiangqie-search-input">
				<icon type="search" size="13" color="#999"></icon>
				<input confirm-type="search" :placeholder="'大家都在搜：' + placeholder" auto-focus="ture"
					placeholder-class="jiangqie-input-plholder" class="jiangqie-input" @confirm="handerSearchConfirm"
					@input="handlerSearchInput">
			</view>

			<view v-if="canSearch" class="jiangqie-search" @tap="handerSearchClick">搜索</view>
			<view v-else class="jiangqie-cancle" @tap="handerCancelClick">取消</view>
		</view>

		<!--搜索模块-->
		<view v-if="historySearch.length>0" class="jiangqie-search-box">
			<view class="jiangqie-search-head">
				<text class="jiangqie-search-clear" @tap.stop="handlerClearHistory">清除历史</text>
				<image src="/static/images/time.png" mode="widthFix" class="jiangqie-search-icon"></image>历史搜索
			</view>
			<view class="jiangqie-search-list">
				<view v-for="(item, index) in historySearch" :key="index" class="jiangqie-search-tag" :data-item="item"
					@tap.stop="handlerSearchItemClick">
					<image src="/static/images/del.png" mode="widthFix" class="jiangqie-search-del" :data-item="item"
						@tap.stop="handlerSearchItemDelete"></image>
					<text>{{item}}</text>
				</view>
			</view>
		</view>

		<!--热门模块-->
		<view v-if="hotSearch.length>0" class="jiangqie-search-box">
			<view class="jiangqie-search-head">
				<image src="/static/images/hot.png" mode="widthFix" class="jiangqie-search-icon"></image>热门
			</view>
			<view class="jiangqie-search-list jiangqie-search-hot">
				<view v-for="(item, index) in hotSearch" :key="index" class="jiangqie-search-tag" :data-item="item"
					@tap.stop="handlerSearchItemClick">
					<text>{{item}}</text>
				</view>
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
	const Api = require("@/utils/api.js");
	const Rest = require("@/utils/rest.js");

	export default {
		components: {
			
		},
		
		data() {
			return {
				canSearch: false,
				historySearch: [],
				hotSearch: [],
				placeholder: '',
				keyword: '',
			};
		},

		onLoad(options) {
			uni.getStorage({
				key: Constant.JQ_SEARCH_KEY,
				success: (res) => {
					this.historySearch = res.data;
				}
			});
			this.placeholder = getApp().globalData.appName;
			Rest.get(Api.JIANGQIE_POSTS_SEARCH_HOT).then(res => {
				this.hotSearch = res.data;
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
			 * 输入 搜索词
			 */
			handlerSearchInput(e) {
				this.keyword = e.detail.value;
				this.canSearch = this.keyword.length > 0;
			},

			/**
			 * 确认 搜索
			 */
			handerSearchConfirm(e) {
				this.search();
			},

			/**
			 * 点击 搜索
			 */
			handerSearchClick(e) {
				this.search();
			},

			/**
			 * 搜索
			 */
			search() {
				uni.getStorage({
					key: Constant.JQ_SEARCH_KEY,
					success: (res) => {
						let keys = [this.keyword];

						for (let i = 0; i < res.data.length && keys.length < Constant
							.JQ_SEARCH_MAX_COUNT; i++) {
							if (this.keyword == res.data[i]) {
								continue;
							}

							keys.push(res.data[i]);
						}

						this.historySearch = keys;
						uni.setStorage({
							data: keys,
							key: Constant.JQ_SEARCH_KEY
						});
					},

					fail: (e) => {
						let keys = [this.keyword];
						this.historySearch = keys;
						uni.setStorage({
							data: keys,
							key: Constant.JQ_SEARCH_KEY
						});
					}

				});
				uni.navigateTo({
					url: '/pages/list/list?search=' + this.keyword
				});
			},

			/**
			 * 取消搜索
			 */
			handerCancelClick(e) {
				Util.navigateBack();
			},

			/**
			 * 清除搜索历史
			 */
			handlerClearHistory(e) {
				uni.showModal({
					title: '提示',
					content: '确定要清除吗？',
					success: (res) => {
						if (res.confirm) {
							uni.setStorage({
								key: Constant.JQ_SEARCH_KEY,
								data: [],
								success: () => {
									this.historySearch = [];
								}
							});
						}
					}
				});
			},

			/**
			 * 点击 搜索历史
			 */
			handlerSearchItemClick(e) {
				let item = e.currentTarget.dataset.item;
				uni.navigateTo({
					url: '/pages/list/list?search=' + item
				});
			},

			/**
			 * 删除 搜索历史
			 */
			handlerSearchItemDelete(e) {
				uni.showModal({
					title: '提示',
					content: '确定要删除吗？',
					success: (res) => {
						if (res.confirm) {
							let item = e.currentTarget.dataset.item;
							let keys = [];

							for (let i = 0; i < this.historySearch.length; i++) {
								if (item == this.historySearch[i]) {
									continue;
								}

								keys.push(this.historySearch[i]);
							}

							this.historySearch = keys;
							uni.setStorage({
								data: keys,
								key: Constant.JQ_SEARCH_KEY
							});
						}
					}
				});
			}
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

	.jiangqie-search-box {
		box-sizing: border-box;
		padding: 30rpx 0;
		border-bottom: 1rpx solid #DDDDDD;
	}

	.jiangqie-search-head {
		text-align: left;
		font-size: 30rpx;
		font-weight: 500;
	}

	.jiangqie-search-icon {
		height: 28rpx;
		width: 28rpx;
		margin-right: 8rpx;
	}

	.jiangqie-search-clear {
		float: right;
		font-size: 24rpx;
		color: #999;
		font-weight: 300;
	}

	.jiangqie-search-list {
		flex-direction: column;
		box-sizing: border-box;
		padding-top: 30rpx;
	}

	.jiangqie-search-tag {
		float: left;
		padding: 4rpx 60rpx 8rpx 26rpx;
		vertical-align: middle;
		background: #EAEAEA;
		border-radius: 40rpx;
		margin-right: 20rpx;
		margin-bottom: 20rpx;
	}

	.jiangqie-search-hot .jiangqie-search-tag {
		padding-right: 26rpx;
	}

	.jiangqie-search-tag text {
		font-weight: 300;
		font-size: 24rpx;
		color: #666;
	}

	.jiangqie-search-del {
		float: right;
		height: 22rpx;
		width: 22rpx;
		margin-right: -36rpx;
		margin-top: 14rpx;
	}

	.jiangqie-searchbox {
		padding: 30rpx 0;
		box-sizing: border-box;
		display: flex;
		align-items: center;
	}

	.jiangqie-search-input {
		width: 100%;
		height: 66rpx;
		border-radius: 35rpx;
		padding: 0 30rpx;
		box-sizing: border-box;
		background: #EEE;
		display: flex;
		align-items: center;
		flex-wrap: nowrap;
		border: 1rpx solid #DDD;
	}

	.jiangqie-input {
		flex: 1;
		color: #333;
		padding: 0 16rpx;
		font-size: 28rpx;
	}

	.jiangqie-input-plholder {
		font-size: 28rpx;
		color: #b2b2b2;
	}

	.jiangqie-cancle {
		color: #888;
		font-size: 28rpx;
		padding-left: 30rpx;
		flex-shrink: 0;
	}

	.jiangqie-search {
		font-size: 28rpx;
		padding-left: 30rpx;
		flex-shrink: 0;
	}
</style>