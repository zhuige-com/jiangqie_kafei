<template>
	<view class="content">
		<view class="jiangqie-verify-box">
			<!-- 用户信息 -->
			<view class="jiangqie-verify-info">
				<button open-type="chooseAvatar" @chooseavatar="onChooseAvatar" class="jiangqie-verify-line">
					<view>
						<image mode="aspectFill" :src="avatar"></image>
					</view>
					<view class="jiangqie-verify-text">点击更换头像</view>
				</button>
				<view class="jiangqie-verify-line">
					<view class="jiangqie-verify-text">你这么帅，不换个昵称吗</view>
					<view>
						<input type="nickname" v-model="nickname" @blur="onNicknameBlur" placeholder="请输入昵称" />
					</view>
				</view>
			</view>

			<!-- 底部大按钮 -->
			<view class="jiangqie-verify-button">
				<view @click="clickSubmit">提交</view>
				<view @click="clickBack">跳过</view>
			</view>
		</view>
	</view>

</template>

<script>
	import Util from '@/utils/util';
	import Auth from '@/utils/auth';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	export default {
		components: {
			
		},
		
		data() {
			return {
				nickname: '',
				avatar: '/static/images/default_avatar.jpg'
			}
		},

		onLoad(options) {
			let user = Auth.getUser();
			if (user) {
				this.nickname = user.nickname;
				if (user.avatar) {
					this.avatar = user.avatar;
				}
			}
		},

		methods: {
			/**
			 * 点击打开链接
			 */
			openLink(link) {
				Util.openLink(link);
			},

			/**
			 * 选择头像
			 */
			onChooseAvatar(e) {
				Rest.upload(Api.JIANGQIE_OTHER_UPLOAD, e.detail.avatarUrl).then(oo => {
					this.avatar = oo.data.src;
				}, err => {
					console.log(err);
				});
			},

			/**
			 * 设置昵称
			 */
			onNicknameBlur(e) {
				this.nickname = e.detail.value;
			},

			/**
			 * 点击提交
			 */
			clickSubmit() {
				Rest.post(Api.JIANGQIE_USER_SET_INFO, {
					nickname: this.nickname,
					avatar: this.avatar
				}).then(res => {
					if (res.code == 0) {
						Util.toast(res.msg);

						let user = Auth.getUser();
						user.nickname = this.nickname;
						user.avatar = this.avatar;
						Auth.setUser(user);

						setTimeout(() => {
							Util.navigateBack();
						}, 1500)
					} else {
						Util.toast(res.msg);
					}
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 点击返回
			 */
			clickBack() {
				Util.navigateBack()
			}
		}
	}
</script>

<style>
	.content {
		position: fixed;
		height: 100%;
		width: 100%;
		overflow-y: scroll;
	}

	.jiangqie-info-form-box {
		padding: 20rpx;
		margin-bottom: 160rpx;
	}

	.jiangqie-verify-box {
		padding: 80rpx 120rpx;
		text-align: center;
	}

	.jiangqie-verify-info {
		padding-bottom: 80rpx;
	}

	.jiangqie-verify-info button,
	.jiangqie-verify-info button::after {
		border: none;
		background: none;
	}

	.jiangqie-verify-line {
		padding: 30rpx 0;
		border-bottom: 1rpx solid #DDDDDD;
	}

	.jiangqie-verify-line image {
		height: 180rpx;
		width: 180rpx;
		border-radius: 50%;
		margin-bottom: 10rpx;
	}

	.jiangqie-verify-text {
		font-size: 30rpx;
		font-weight: 500;
	}

	.jiangqie-verify-line input {
		font-size: 30rpx;
		height: 72rpx;
		line-height: 72rpx;
	}

	.jiangqie-verify-button {
		display: flex;
		align-items: center;
		flex-wrap: wrap;
		justify-content: center;
	}

	.jiangqie-verify-button view {
		width: 360rpx;
		text-align: center;
		height: 96rpx;
		line-height: 96rpx;
		border-radius: 96rpx;
		font-size: 28rpx;
		font-weight: 400;
		color: #999999;
		margin-bottom: 40rpx;
	}

	.jiangqie-verify-button view:nth-child(1) {
		color: #FFFFFF;
		background: #010101;
		font-size: 32rpx;
	}
</style>