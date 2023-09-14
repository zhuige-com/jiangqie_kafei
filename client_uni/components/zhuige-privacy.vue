<template>

	<view class="zhugie-pop-cover" v-if="show">
		<view class="zhuige-secret">
			<view class="zhuige-secret-title">隐私保护指引</view>
			<view class="zhuige-secret-info">
				<text>在继续使用本小程序之前，请查看</text>
				<text class="button" @click="handleOpenPrivacyContract">{{privacyContractName}}</text>。
				<text>如果同意，请点击【同意】继续使用</text>
			</view>
			<view class="zhuige-secret-btn">
				<button @click="handleReject">拒绝</button>
				<button open-type="agreePrivacyAuthorization" @agreeprivacyauthorization="handleAgree">同意</button>
			</view>
		</view>
	</view>

</template>

<script>
	export default {
		name: "zhuige-privacy",

		data() {
			return {
				show: false,
				privacyContractName: '隐私协议'
			};
		},

		created() {
			const version = uni.getAppBaseInfo().SDKVersion;
			if (this.compareVersion(version, '2.32.3') >= 0) {
				wx.getPrivacySetting({
					success: res => {
						this.show = res.needAuthorization;
						this.privacyContractName = res.privacyContractName
					},
					fail: () => {},
					complete: () => {}
				})
			}
		},

		methods: {
			/**
			 * 拒绝协议
			 */
			handleReject() {
				uni.showToast({
					icon: 'none',
					title: '同意后，才能继续使用小程序'
				})
			},

			/**
			 * 同意协议
			 */
			handleAgree() {
				this.show = false;
			},

			/**
			 * 打开隐私协议页面
			 */
			handleOpenPrivacyContract() {
				wx.openPrivacyContract({
					success: () => {}, // 打开成功
					fail: () => {}, // 打开失败
					complete: () => {}
				})
			},

			/**
			 * 比较版本号
			 */
			compareVersion(v1, v2) {
				v1 = v1.split('.')
				v2 = v2.split('.')
				const len = Math.max(v1.length, v2.length)

				while (v1.length < len) {
					v1.push('0')
				}
				while (v2.length < len) {
					v2.push('0')
				}

				for (let i = 0; i < len; i++) {
					const num1 = parseInt(v1[i])
					const num2 = parseInt(v2[i])

					if (num1 > num2) {
						return 1
					} else if (num1 < num2) {
						return -1
					}
				}

				return 0
			},
		}
	}
</script>

<style>
	/* 弹框遮罩 */
	.zhugie-pop-cover {
		position: fixed;
		height: 100%;
		width: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
		background: rgba(0, 0, 0, .6);
		z-index: 998;
		top: 0;
		left: 0;
	}

	/* =========== 隐私保护 =========== */
	.zhuige-secret {
		background: #FFFFFF;
		border-radius: 16rpx;
		padding: 60rpx 40rpx;
		width: 600rpx;
	}

	.zhuige-secret-title {
		font-size: 36rpx;
		font-weight: 500;
		text-align: center;
	}

	.zhuige-secret-info {
		padding: 30rpx 0;
		font-size: 28rpx;
		font-weight: 400;
		color: #555555;
		line-height: 1.8em;
	}

	.zhuige-secret-info .button {
		color: #468AEE;
		margin: 0 4rpx;
	}


	.zhuige-secret-btn {
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.zhuige-secret-btn button {
		background: #010101;
		color: #FFFFFF;
		width: 48%;
		text-align: center;
	}

	.zhuige-secret-btn button::after {
		border: none;
	}

	.zhuige-secret-btn button:nth-child(1) {
		background: #F4F4F5;
		color: #91959B;
	}
</style>