<template>
	<view class="none_tip">
		<image class="image" src="/static/images/none_tip.png" mode="widthFix"></image>
		<view class="none_tip_text">
			<text class="text">什么也没有~</text>
		</view>
	</view>
</template>

<script>
	export default {
		name: "JiangqieNoData",
		
		data() {
			return {};
		},

		components: {},
		
		props: {},
		
		methods: {}
	};
</script>

<style lang="scss" scoped>
	.none_tip {
		padding: 80rpx;
		text-align: center;
	}

	.none_tip .image {
		height: 200rpx;
		width: 200rpx;
	}

	.none_tip_text {
		padding: 20rpx;
	}

	.none_tip_text .text {
		font-size: 28rpx;
		color: #999;
		font-weight: 200;
	}
</style>
