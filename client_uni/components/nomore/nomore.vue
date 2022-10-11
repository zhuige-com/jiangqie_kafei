<template>
	<view class="jiangqie-nomore-class jiangqie-loadmore" v-if="visible">
		<view :class="isDot?'jiangqie-nomore-dot':'jiangqie-nomore'">
			<view :style="'background:' + bgcolor" :class="isDot?'jiangqie-dot-text':'jiangqie-text'">{{isDot?dotText:text}}
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name: "JiangqieNomore",
		
		data() {
			return {
				dotText: "●"
			};
		},

		components: {},
		props: {
			//是否可见
			visible: {
				type: Boolean,
				default: false
			},
			//当前页面背景颜色
			bgcolor: {
				type: String,
				default: "#FFFFFF"
			},
			//是否以圆点代替 "没有更多了"
			isDot: {
				type: Boolean,
				default: false
			},
			//isDot为false时生效
			text: {
				type: String,
				default: "© 追格 zhuige.com"
			}
		},
		externalClasses: ['jiangqie-nomore-class'],
		methods: {}
	};
</script>

<style lang="scss" scoped>
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
		color: #999;
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

</style>
