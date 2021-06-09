<template>
	<cover-view :class="{'jiangqie-poplogin-hidden':!show}" class="jiangqie-poplogin-mask">
		<cover-view class="jiangqie-poplogin">
			<cover-view class="jiangqie-pop-head">
				<cover-image class="cover-image" src="/static/images/default_avatar.jpg" mode="aspectFill"></cover-image>
				<cover-view class="cover-view">立即登录，体验更多功能</cover-view>
			</cover-view>
			<cover-view class="jiangqie-pop-opt">
				<button class="jiangqie-pop-btnl" @tap.stop="onTapCancel">取消</button>
				<button class="jiangqie-pop-btnr" @tap.stop="onTapLogin">登录</button>
			</cover-view>
		</cover-view>
	</cover-view>
</template>

<script>
	export default {
		name: "JiangQiePopLogin",

		data() {
			return {};
		},

		components: {},

		props: {
			show: {
				type: Boolean,
				default: true
			},
		},

		methods: {
			onTapCancel() {
				this.$emit("clickLoginCancel");
			},
			
			onTapLogin() {
				this.$emit("clickLogin");
			}
		}
	};
</script>

<style lang="scss" scoped>
	.jiangqie-poplogin-hidden {
		display: none;
	}

	.jiangqie-poplogin-mask {
		height: 100%;
		width: 100%;
		position: fixed;
		top: 0;
		/*
		   #ifdef  H5  
		 */
		top: calc(88rpx + constant(safe-area-inset-top));
		top: calc(88rpx + env(safe-area-inset-top));
		/*
		   #endif  
		 */
		background: rgba(0, 0, 0, 0.3);
		text-align: center;
		z-index: 199;
	}

	.jiangqie-poplogin {
		width: 600rpx;
		margin: 360rpx auto;
		background: #FFF;
		border-radius: 16rpx;
	}

	.jiangqie-pop-head {
		text-align: left;
		padding: 70rpx 60rpx;
	}

	.jiangqie-pop-head .cover-image {
		height: 72rpx;
		width: 72rpx;
		border-radius: 72rpx;
		margin-right: 30rpx;
		vertical-align: middle;
		float: left;
	}

	.jiangqie-pop-head .cover-view {
		font-size: 30rpx;
		color: #999;
		line-height: 96rpx;
		font-weight: 300;
		float: left;
	}

	.jiangqie-pop-opt {
		overflow: hidden;
		border-top: 1rpx solid #DDD;

	}

	.jiangqie-pop-btnl,
	.jiangqie-pop-btnr {
		height: 80rpx;
		width: 299rpx;
		background: #FFF;
		font-size: 32rpx;
		color: #999;
		float: left;
		font-weight: 300;
		border-radius: 0 0 0 16rpx;
	}

	.jiangqie-pop-btnr {
		float: right;
		color: #555;
		border-left: 2rpx solid #DDD;
		border-radius: 0 0 16rpx 0;
	}
</style>
