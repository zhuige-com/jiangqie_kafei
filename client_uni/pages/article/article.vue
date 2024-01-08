<template>
	<view>
		<view class="container jiangqie-page-view">
			<view class="jiangqie-page-head">
				<view class="jiangqie-page-title">
					<text>{{post.title}}</text>
					<view class="jiangqie-page-cmt">
						<view class="jiangqie-page-cmt-info">
							<image src="/static/images/ctm2.png" mode="widthFix" class="jiangqie-page-icon"></image>
							{{post.views}}
						</view>
						<text>{{post.time}}</text>
						<text v-for="(cat, cati) in post.cats" :key="cati" class="category">{{cat.name}}</text>
					</view>
				</view>
			</view>

			<!-- #ifdef MP-WEIXIN -->
			<view v-if="official" class="jiangqie-official-mod">
				<official-account></official-account>
			</view>

			<template v-if="wx_ad_top">
				<ad :unit-id="wx_ad_top"></ad>
			</template>
			<!-- #endif -->

			<view class="jiangqie-page-body">
				<mp-html :content="article"></mp-html>

				<view class="jiangqie-page-body-end">
					<text>- The End -</text>
				</view>
				<view class="jiangqie-page-body-tag">
					<text v-for="(item, index) in post.tags" :key="index" :data-id="item.id" :data-tag="item.name"
						@tap.stop="handlerTagClick">{{item.name}}</text>
				</view>
				<view class="jiangqie-page-laud">
					<view class="jiangqie-page-laud-contrl">
						<view v-if="islike==1" class="jiangqie-page-laud-btn" @tap.stop="handlerLikeClick">
							<image src="/static/images/laud_on.png" mode="widthFix"></image>
							<text>已赞过</text>
						</view>
						<view v-else class="jiangqie-page-laud-btn" @tap.stop="handlerLikeClick">
							<image src="/static/images/laud.png" mode="widthFix"></image>
							<text>点个赞</text>
						</view>
						<button class="jiangqie-page-laud-btn" @tap.stop="sharePosterClick">
							<image src="/static/images/share.png" mode="widthFix"></image>
							<text>海报分享</text>
						</button>
					</view>
					<view class="jiangqie-page-laud-list">
						<view class="jiangqie-page-laud-list-title">- {{like_list.length}}人已赞 -</view>
						<view class="jiangqie-page-laud-list-block">
							<image v-for="(item, index) in like_list" :key="index"
								:src="item.length>0?item:'/static/images/default_avatar.jpg'" mode="aspectFill"></image>
						</view>
					</view>
				</view>
			</view>

			<view v-if="pre_next" class="jiangqie-pre-next-view">
				<view @click="clickNext" class="jiangqie-next-view">
					<template v-if="next">
						<uni-icons type="arrowleft" size="24"></uni-icons>上一篇
					</template>
				</view>
				<view @click="clickPre" class="jiangqie-pre-view">
					<template v-if="pre">
						下一篇<uni-icons type="arrowright" size="24"></uni-icons>
					</template>
				</view>
			</view>

			<!-- #ifdef MP-WEIXIN -->
			<template v-if="wx_ad_bottom">
				<ad :unit-id="wx_ad_bottom"></ad>
			</template>
			<!-- #endif -->

			<view class="jiangqie-page-cmtbox">
				<view class="jiangqie-page-cmt-title">
					评论<text>{{comment_count + comment_count_change}}</text>
				</view>
				<template v-if="comments.length>0">
					<view v-for="(item, index) in comments" :key="index" class="jiangqie-page-cmt-content">
						<view class="jiangqie-page-cmt-avatar">
							<image :src="item.user.avatar.length>0?item.user.avatar:'/static/images/default_avatar.jpg'"
								mode="aspectFill"></image>
						</view>
						<view class="jiangqie-page-cmt-head">
							{{item.user.name}}
							<text class="jiangqie-page-cmt-time">{{item.time}}</text>
							<text v-if="item.approved!=1" class="jiangqie-page-cmt-time">待审核</text>
							<text class="comment-action" :data-id="item.id"
								@tap.stop="handlerCommentReplyClick">回复</text>
							<text v-if="item.user.is_me==1" class="comment-action" :data-id="item.id"
								@tap.stop="handlerCommentDeleteClick">删除</text>
						</view>
						<view class="jiangqie-page-cmt-text">
							<text class="jiangqie-page-cmt-text-world">{{item.content}}</text>
							<view class="jiangqie-page-cmt-replay">
								<view v-for="(reply, index2) in item.replys" :key="index2"
									class="jiangqie-page-cmt-replay-box">
									<view class="jiangqie-page-cmt-replay-nametime">
										{{reply.user.name}}
										<text class="jiangqie-page-cmt-replay-time">{{reply.time}}</text>
										<text v-if="reply.approved!=1" class="jiangqie-page-cmt-time">待审核</text>
										<text v-if="reply.user.is_me==1" class="comment-action" :data-id="reply.id"
											@tap.stop="handlerCommentDeleteClick">删除</text>
									</view>
									<view class="jiangqie-page-cmt-replay-world">{{reply.content}}</view>
								</view>
							</view>
						</view>
					</view>
					<!--加载loadding-->
					<jiangqie-loadmore :visible="loadding"></jiangqie-loadmore>
					<jiangqie-nomore :visible="!pullUpOn"></jiangqie-nomore>
					<!--加载loadding-->
				</template>
				<template v-else-if="loaded">
					<jiangqie-no-data></jiangqie-no-data>
				</template>
			</view>
		</view>

		<!-- 操作栏 -->
		<view class="jiangqie-operation" v-if="!show_comment_submit && post.switch_comment==1">
			<view class="jiangqie-operation-left jiangqie-col-7 jiangqie-height-full jiangqie-ptop-zero">
				<view class="jiangqie-btn-comment" @tap.stop="handlerCommentClick">发表你的评论...</view>
			</view>
			<view class="jiangqie-operation-right jiangqie-right-flex jiangqie-col-5">
				<view class="jiangqie-operation-item" hover-class="opcity" hover-stay-time="150"
					@tap="handlerCommentClick">
					<image src="/static/images/btn_com.png" mode="widthFix"></image>
					<text>{{comment_count + comment_count_change}}</text>
				</view>
				<view class="jiangqie-operation-item" @tap.stop="handlerFavoriteClick">
					<template v-if="isfavorite==1">
						<image src="/static/images/btn_fav_on.png" mode="widthFix"></image>
					</template>
					<template v-else>
						<image src="/static/images/btn_fav.png" mode="widthFix"></image>
					</template>
					<text>{{favorite_count}}</text>
				</view>
				<button open-type="share" class="jiangqie-operation-item" hover-class="opcity" hover-stay-time="150">
					<image src="/static/images/btn_share.png" mode="widthFix"></image>
				</button>
			</view>
		</view>

		<!-- 发表评论 -->
		<view capture-catch:touchmove class="textareacontent" v-if="show_comment_submit">
			<form @submit="handlerCommentSubmit">
				<view class="textheaders">
					<view @tap.stop="handlerCancelClick" class="cancel">取消</view>
					<button form-type="submit" class="publish">发布</button>
				</view>
				<view class="textareaBox">
					<view class="textNum">{{comment_content.length}}/200</view>
					<textarea auto-height autoFocus="true" name="inputComment" @input="handlerContentInput"
						class="textareaInput" maxlength="200" :show-confirm-bar="false"
						:placeholder="placeholder"></textarea>
				</view>
			</form>
		</view>
		<view @tap.stop="handlerCancelClick" class="pagemake" v-if="show_comment_submit"></view>

		<view class="jiangqie-goback-btn" @tap="jumpBtn">
			<image src="/static/images/goback.png" mode="widthFix"></image>
		</view>

		<!-- #ifdef MP-BAIDU -->
		<view v-if="isShowPainter" isRenderImage style="position: fixed; top: 0;" @longpress="longTapPainter"
			@click="clickPainter()">
			<l-painter isRenderImage :board="base" @success="onPainterSuccess" />
		</view>
		<!-- #endif -->

		<!-- #ifdef MP-WEIXIN || H5 -->
		<l-painter v-if="isShowPainter" isRenderImage custom-style="position: fixed; left: 200%;" :board="base"
			@success="onPainterSuccess" />
		<!-- #endif -->
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

	import JiangqieLoadmore from "@/components/loadmore/loadmore";
	import jiangqieNomore from "@/components/nomore/nomore";
	import JiangqieNoData from "@/components/nodata/nodata";
	import lPainter from '@/uni_modules/lime-painter/components/lime-painter/';

	const Constants = require("@/utils/constants.js");
	const Util = require("@/utils/util.js");
	const Api = require("@/utils/api.js");
	const Rest = require("@/utils/rest.js");
	const Auth = require("@/utils/auth.js");
	const Extend = require("@/utils/extend.js");

	export default {
		components: {
			JiangqieLoadmore,
			jiangqieNomore,
			JiangqieNoData,
			lPainter
		},
		
		data() {
			this.comment_id = 0;

			//小程序码
			this.acode = '';

			//返回页面是否需要刷新
			this.needRefresh = true;

			// 是否正在提交评论
			this.comment_submiting = false;

			return {
				post: {
					title: "",
					views: "",
					time: "",
					cats: [],
					tags: [],
					switch_comment: 0
				},
				islike: 0,
				isfavorite: 0,
				favorite_count: 0,
				comment_count: 0,
				comments: [],
				loadding: false,
				pullUpOn: true,
				loaded: false,
				show_comment_submit: false,
				comment_content: '',
				comment_count_change: 0,
				post_id: 0,

				wx_ad_top: undefined,
				wx_ad_bottom: undefined,

				pre_next: undefined,
				pre: undefined,
				next: undefined,

				article: "",
				official: false,
				like_list: [],
				placeholder: "",

				isShowPainter: false,
				painterImage: '',
				base: undefined,
			};
		},

		onLoad(options) {
			if (options.scene) {
				this.post_id = decodeURIComponent(options.scene);
			} else if (options.post_id) {
				this.post_id = options.post_id;
			} //小程序码

			// #ifdef MP-WEIXIN
			this.loadWxacode();
			// #endif

			// #ifdef MP-BAIDU || H5
			this.loadBdacode();
			// #endif
			
			uni.$on('linktap', this.onMPHtmlLink);
		},

		onShow() {
			if (!this.needRefresh) {
				this.needRefresh = true;
				return;
			}

			Rest.get(Api.JIANGQIE_POST_DETAIL, {
				post_id: this.post_id
			}).then(res => {
				uni.setNavigationBarTitle({
					title: res.data.title
				});

				this.post = res.data;
				this.islike = res.data.user.islike;
				this.isfavorite = res.data.user.isfavorite;
				this.favorite_count = res.data.favorite_count;
				this.comment_count = Number(res.data.comment_count);
				this.like_list = res.data.like_list;

				this.article = res.data.content;

				this.wx_ad_top = res.data.wx_ad_top;
				this.wx_ad_bottom = res.data.wx_ad_bottom;
				this.pre_next = res.data.pre_next;
				if (this.pre_next) {
					this.pre = res.data.pre;
					this.next = res.data.next;
				}

				// #ifdef MP-BAIDU
				let keywords = [];
				this.post.tags.forEach(tag => {
					keywords.push(tag.name)
				})
				swan.setPageInfo({
					title: this.post.title,
					keywords: keywords.join(','),
					description: this.post.excerpt,
				});
				// #endif
			});
			this.loadComments(true);

			this.comment_submiting = false;
		},
		
		onUnload() {
			uni.$off('linktap', this.onMPHtmlLink);
		},

		onReachBottom() {
			if (!this.pullUpOn) {
				return;
			}

			this.loadComments(false);
		},

		onShareAppMessage() {
			return {
				title: this.post.title,
				imageUrl: this.post.thumbnail,
				path: 'pages/article/article?post_id=' + this.post_id
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: this.post.title,
				query: 'post_id=' + this.post_id,
				imageUrl: this.post.thumbnail
			};
		},
		// #endif

		methods: {
			/**
			 * 点击文章内链接
			 */
			onMPHtmlLink(data) {
				Extend.clickHref(data);
			},
			
			//海报分享-百度
			// #ifdef MP-BAIDU
			clickPainter() {
				this.isShowPainter = false;
			},

			longTapPainter() {
				uni.showActionSheet({
					itemList: ['保存到相册'],
					success: (res) => {
						if (res.tapIndex == 0) {
							uni.showLoading({
								title: '导出……'
							})
							let save2album = setInterval(() => {
								if (!this.painterImage || this.painterImage.length == 0) {
									return;
								}
								clearInterval(save2album)
								uni.hideLoading();

								uni.saveImageToPhotosAlbum({
									filePath: this.painterImage,
									success: () => {
										Util.toast('已保存');
									},
									fail: (res) => {
										if (res.errMsg && res.errMsg.indexOf('cancel') < 0) {
											Util.toast(res.errMsg);
										}
									}
								})
							}, 500);
						}
					},
					fail: (res) => {
						console.log(res.errMsg);
					}
				});
			},
			// #endif

			//海报分享
			sharePosterClick() {
				// #ifndef MP-BAIDU
				if (this.painterImage) {
					uni.previewImage({
						urls: [this.painterImage]
					});
					return;
				}
				// #endif
				
				uni.showLoading({
					title: '海报生成中……'
				});

				this.isShowPainter = true;
				
				this.base = {
					width: '750rpx',
					height: '1334rpx',
					backgroundColor: '#E6372F',
					views: [{
							type: 'view',
							css: {
								left: '30rpx',
								top: '234rpx',
								width: '690rpx',
								height: '1000rpx',
								background: '#FFFFFF',
							}
						},
						{
							type: 'text',
							text: this.post.title,
							css: {
								left: '30rpx',
								top: '120rpx',
								width: '690rpx',
								color: '#FFFFFF',
								fontSize: '38rpx',
								textAlign: 'center',
								maxLines: 1,
							}
						},
						{
							type: 'image',
							src: this.post.thumbnail,
							mode: 'center',
							css: {
								left: '30rpx',
								top: '200rpx',
								width: '690rpx',
								height: '520rpx'
							}
						},
						{
							type: 'text',
							text: this.post.excerpt,
							css: {
								left: '70rpx',
								top: '740rpx',
								width: '610rpx',
								color: '#000000',
								fontSize: '28rpx',
								maxLines: 3,
							}
						},
						{
							type: 'image',
							src: this.acode,
							mode: 'aspectFill',
							css: {
								left: '275rpx',
								top: '920rpx',
								width: '200rpx',
								height: '200rpx'
							}
						},
						{
							type: 'text',
							text: getApp().globalData.appName,
							css: {
								left: '30rpx',
								top: '1170rpx',
								width: '690rpx',
								color: '#888888',
								fontSize: '28rpx',
								textAlign: 'center',
							}
						},
					]
				}
			},

			onPainterSuccess(e) {
				this.painterImage = e;

				// #ifndef MP-BAIDU
				uni.previewImage({
					urls: [e]
				});
				// #endif
				
				uni.hideLoading();
			},

			/**
			 * 点击 TAG
			 */
			handlerTagClick(e) {
				let tag_id = e.currentTarget.dataset.id;
				let tag = e.currentTarget.dataset.tag;
				uni.navigateTo({
					url: '/pages/list/list?title=' + tag + '&tag_id=' + tag_id
				});
			},

			/**
			 * 跳转返回
			 */
			jumpBtn() {
				Util.navigateBack();
			},

			/**
			 * 文章 点赞
			 */
			handlerLikeClick(e) {
				Rest.get(Api.JIANGQIE_USER_LIKE, {
					post_id: this.post.id
				}).then(res => {
					let avatar = Auth.getUser().avatar;
					var index = this.like_list.indexOf(avatar);

					if (index > -1) {
						this.like_list.splice(index, 1);
					} else {
						this.like_list.unshift(avatar);
					}

					this.islike = (this.islike == 1 ? 0 : 1);
					this.like_list = this.like_list;
				});
			},

			/**
			 * 评论 弹框
			 */
			handlerCommentClick(e) {
				this.comment_id = 0;
				this.show_comment_submit = true
			},

			/**
			 * 评论 取消
			 */
			handlerCancelClick(e) {
				this.show_comment_submit = false
			},

			/**
			 * 评论 提交
			 */
			handlerCommentSubmit(e) {
				if (!this.comment_content) {
					Util.toast('请输入内容');
					return;
				}

				if (this.comment_submiting) {
					return;
				}
				this.comment_submiting = true;

				Rest.get(Api.JIANGQIE_COMMENT_ADD, {
					post_id: this.post_id,
					parent_id: this.comment_id,
					content: this.comment_content
				}).then(res => {
					this.comment_count_change = this.comment_count_change + (res.data.comment_verify ==
						1 ? 0 : 1);
					this.show_comment_submit = false;
					this.loadComments(true);

					this.comment_submiting = false;
				});
			},

			/**
			 * 评论 回复
			 */
			handlerCommentReplyClick(e) {
				this.comment_id = e.currentTarget.dataset.id;
				this.show_comment_submit = true;
			},

			/**
			 * 评论 删除
			 */
			handlerCommentDeleteClick(e) {
				uni.showModal({
					title: '提示',
					content: '确定要删除吗？',
					success: (res) => {
						if (res.confirm) {
							let comment_id = e.currentTarget.dataset.id;
							Rest.get(Api.JIANGQIE_COMMENT_DELETE, {
								comment_id: comment_id
							}).then(res => {
								this.comment_count_change = this.comment_count_change - 1;
								this.loadComments(true);
							});
						}
					}
				});
			},

			/**
			 * 评论输入
			 */
			handlerContentInput(e) {
				this.comment_content = e.detail.value;
			},

			/**
			 * 文章 收藏
			 */
			handlerFavoriteClick(e) {
				Rest.get(Api.JIANGQIE_USER_FAVORITE, {
					post_id: this.post.id
				}).then(res => {
					// this.isfavorite = (this.isfavorite == 1 ? 0 : 1);
					this.isfavorite = res.data.isfavorite;
					this.favorite_count = res.data.favorite_count;
				});
			},

			/**
			 * 上一篇
			 */
			clickPre() {
				if (this.pre) {
					Util.openLink('/pages/article/article?post_id=' + this.pre)
				}
			},

			/**
			 * 下一篇
			 */
			clickNext() {
				if (this.next) {
					Util.openLink('/pages/article/article?post_id=' + this.next)
				}
			},

			/**
			 * 加载微信小程序码
			 */
			loadWxacode() {
				Rest.get(Api.JIANGQIE_POST_WX_ACODE, {
					post_id: this.post_id
				}).then(res => {
					this.acode = res.data;
				}, err => {
					console.log(err);
				});
			},

			/**
			 * 加载百度小程序码
			 */
			loadBdacode() {
				Rest.get(Api.JIANGQIE_POST_BD_ACODE, {
					post_id: this.post_id
				}).then(res => {
					this.acode = res.data;
				}, err => {
					console.log(err);
				});
			},

			/**
			 * 加载 评论
			 */
			loadComments(refresh) {
				this.loadding = true;
				let offset = 0;
				if (!refresh) {
					offset = this.comments.length;
				}

				Rest.get(Api.JIANGQIE_COMMENT_INDEX, {
					post_id: this.post_id,
					offset: offset
				}).then(res => {
					this.loaded = true;
					this.loadding = false;
					this.comments = (refresh ? res.data : this.comments.concat(res.data));
					this.pullUpOn = (res.data.length >= Constants.JQ_PER_PAGE_COUNT);
				});
			},

		}
	};
</script>
<style lang="scss" scoped>
	button {
		margin: 0;
		padding: 0;
		border-radius: 0;
		background-color: transparent;
	}

	.jiangqie-page-view {
		padding: 0rpx;
	}

	.jiangqie-page-head {
		position: sticky;
		overflow: hidden;
		border-bottom: 4rpx solid #F3F3F3;
	}

	.jiangqie-page-title {
		width: 100%;
	}

	.jiangqie-page-title text {
		display: block;
		line-height: 46rpx;
		padding: 50rpx 30rpx 0;
		color: #333;
		font-size: 36rpx;
		font-weight: 500;
	}

	.jiangqie-page-cmt {
		height: 80rpx;
		line-height: 80rpx;
		border-radius: 40rpx 40rpx 0 0;
		background: #FFF;
		padding: 0 30rpx;
	}

	.jiangqie-page-cmt-info {
		float: right;
		font-size: 24rpx;
		font-weight: 400;
		color: #999;
	}

	.jiangqie-page-icon {
		height: 32rpx;
		width: 32rpx;
		margin-right: 10rpx;
		vertical-align: text-bottom;
		margin-bottom: 2rpx;
	}

	.jiangqie-page-cmt text {
		font-size: 24rpx;
		font-weight: 400;
		color: #999;
		display: inline-block;
		line-height: 80rpx;
		padding: 0rpx;
	}

	.jiangqie-page-cmt .category {
		margin-left: 20rpx;
	}

	.jiangqie-page-title-img {
		position: relative;
		width: 100%;
	}

	.jiangqie-page-body {
		padding: 30rpx 50rpx;
		background: #FFF;
		line-height: 2.2rem;
		font-weight: 400;
		font-size: 30rpx;
	}

	.jiangqie-page-body rich-text {
		line-height: 2rem;
		min-height: 2rem;
		display: block;
	}

	.wxParse-p {
		margin-top: 30rpx;
		font-size: 30rpx;
		color: #333333;
		line-height: 2rem;
		font-weight: 300;
	}

	.jiangqie-page-body-end {
		height: 100rpx;
		line-height: 100rpx;
		font-size: 20rpx;
		color: #CCC;
		text-align: center
	}

	.jiangqie-page-body-tag {
		padding: 20rpx 0;
		overflow: hidden;
		border-bottom: 1rpx solid #EEE;
	}

	.jiangqie-page-body-tag text {
		float: left;
		padding: 4rpx 22rpx 6rpx 22rpx;
		vertical-align: middle;
		background: #F6F6F6;
		border-radius: 40rpx;
		margin-right: 20rpx;
		margin-bottom: 20rpx;
		font-weight: 300;
		font-size: 22rpx;
		color: #666;
	}

	.jiangqie-page-body-banner {
		height: 160rpx;
		width: 690rpx;
		overflow: hidden;
		border-radius: 20rpx;
		box-shadow: 5rpx 5rpx 20rpx rgba(0, 0, 0, 0.2);
		margin: 20rpx 0;
	}

	.jiangqie-page-body-banner image {
		width: 100%;
	}

	.jiangqie-page-cmtbox {
		padding: 20rpx 30rpx 80rpx;
		border-top: 16rpx solid #F3F3F3;
		background: #FFF;
		clear: left;
	}

	.jiangqie-page-cmt-title {
		font-size: 32rpx;
		font-weight: 500;
		color: #333;
		padding: 10rpx 0 16rpx;
	}

	.jiangqie-page-cmt-title text {
		font-size: 20rpx;
		font-weight: 400;
		color: #999;
		padding: 6rpx 16rpx;
		border-radius: 30rpx;
		background: #F6F6F6;
		margin-left: 30rpx;
	}

	.jiangqie-page-cmt-content {
		padding: 20rpx 0;
		padding-left: 80rpx;
		border-top: 1rpx solid #EEE;
		overflow: hidden;
	}

	.jiangqie-page-cmt-avatar {
		float: left;
		margin-left: -80rpx;
		height: 64rpx;
		width: 64rpx;
	}

	.jiangqie-page-cmt-avatar image {
		height: 64rpx;
		width: 64rpx;
		border-radius: 64rpx;
		overflow: hidden;
	}

	.jiangqie-page-cmt-head {
		font-size: 28rpx;
		font-weight: 500;
		color: #333;
	}

	.jiangqie-page-cmt-time {
		font-size: 22rpx;
		font-weight: 400;
		color: #888;
		margin-left: 20rpx;
	}

	.jiangqie-page-cmt-text {
		padding: 10rpx 0;
	}

	.jiangqie-page-cmt-text-world {
		font-size: 26rpx;
		font-weight: 300;
		color: #444;
	}

	.jiangqie-page-cmt-replay {
		margin-top: 20rpx;
		border-radius: 30rpx;
		padding: 0 20rpx;
		background: #F6F6F6;
	}

	.jiangqie-page-cmt-replay-box {
		padding: 20rpx 0;
		border-bottom: 1rpx solid #EEE;
	}

	.jiangqie-page-cmt-replay-box:last-child {
		border-bottom: none;
	}

	.jiangqie-page-cmt-replay-nametime {
		padding-bottom: 10rpx;
		font-size: 24rpx;
		font-weight: 500;
		color: #333;
	}

	.jiangqie-page-cmt-replay-time {
		font-size: 20rpx;
		font-weight: 400;
		color: #BBB;
		margin-left: 20rpx;
	}

	.jiangqie-page-cmt-replay-world {
		font-size: 24rpx;
		font-weight: 300;
		color: #444;
	}

	.jiangqie-page-laud {
		overflow: hidden;
		border-bottom: 1rpx solid #EEE;
	}

	.jiangqie-page-laud-contrl {
		padding: 40rpx 20rpx 10rpx;
		overflow: hidden;
	}

	.jiangqie-page-laud-btn {
		font-size: 14px;
		text-align: center;
		float: left;
		margin-left: 40rpx;
		border-radius: 80rpx;
		padding: 5rpx 40rpx 7rpx;
		width: 180rpx;
		border: 1rpx solid #CCCCCC;
		line-height: 1.8rem;
	}

	.jiangqie-page-laud-btn image {
		height: 36rpx;
		width: 36rpx;
		margin-right: 12rpx;
		vertical-align: text-bottom;
	}

	.jiangqie-page-laud-btn text {
		font-size: 26rpx;
		font-weight: 400;
		color: #999;
	}

	.jiangqie-page-laud-list {
		padding: 20rpx 0rpx 10rpx;
	}

	.jiangqie-page-laud-list-title {
		text-align: center;
		padding: 20rpx;
		font-size: 22rpx;
		color: #BBB;
		font-weight: 200;
	}

	.jiangqie-page-laud-list-block {
		padding: 20rpx 0;
		overflow: hidden;
		text-align: center;
	}

	.jiangqie-page-laud-list-block image {
		height: 64rpx;
		width: 64rpx;
		margin: 8rpx 2rpx;
		border-radius: 64rpx;
	}

	.jiangqie-operation {
		width: 100%;
		height: 100rpx;
		overflow: hidden;
		background: #fff;
		display: flex;
		align-items: center;
		justify-content: space-between;
		position: fixed;
		left: 0;
		bottom: 0;
		z-index: 99999;
		padding-bottom: env(safe-area-inset-bottom);
	}

	.jiangqie-safearea-bottom {
		width: 100%;
		height: env(safe-area-inset-bottom);
	}

	.jiangqie-operation::before {
		content: '';
		position: absolute;
		top: 0;
		right: 0;
		left: 0;
		border-top: 1rpx solid #eaeef1;
		-webkit-transform: scaleY(0.5);
		transform: scaleY(0.5);
	}

	.jiangqie-operation-left {
		display: flex;
		align-items: center;
	}

	.jiangqie-operation-item {
		flex: 1;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-direction: column;
		position: relative;
	}

	.jiangqie-operation-item image {
		height: 42rpx;
		width: 42rpx;
	}

	.jiangqie-operation-item text {
		position: absolute;
		right: 10rpx;
		background: #FFF;
		font-size: 20rpx;
		top: -10rpx;
		padding: 4rpx;
	}

	.jiangqie-operation-right {
		height: 100rpx;
		box-sizing: border-box;
		padding-top: 0;
	}

	.jiangqie-badge-class {
		color: #5677fc !important;
		position: absolute;
		top: -6rpx;
		padding: 2px 4px !important;
	}

	.jiangqie-right-flex {
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.jiangqie-btn-comment {
		height: 64rpx;
		width: 84%;
		background: #ededed;
		color: #999;
		border-radius: 16rpx;
		font-size: 28rpx;
		display: flex;
		align-items: center;
		padding-left: 20rpx;
		box-sizing: border-box;
		padding-top: 0;
		margin-left: 30rpx;
	}

	.jiangqie-col-7 {
		width: 58.33333333%;
	}

	.jiangqie-col-5 {
		width: 41.66666667%;
	}

	.jiangqie-share-btn {
		display: block;
		background: none;
		margin: 0;
		padding: 0;
		border: none;
	}

	.comment-action {
		margin-left: 20rpx;
	}


	/* 评论框 -- start --*/
	.textareacontent {
		position: fixed;
		top: 350rpx;
		width: 100%;
		height: 1200rpx;
		border-top-left-radius: 16rpx;
		border-top-right-radius: 16rpx;
		background-color: #fff;
		z-index: 10;
	}

	.textareacontent .textheaders {
		width: 100%;
		height: 96rpx;
		border-bottom: 1rpx solid #eaeaea;
	}

	.textareacontent .textheaders .cancel {
		color: #333;
		font-size: 32rpx;
		line-height: 96rpx;
		margin-left: 32rpx;
		float: left;
	}

	.textareacontent .textheaders .publish {
		color: #3ec382;
		font-size: 32rpx;
		line-height: 96rpx;
		margin-right: 32rpx;
		float: right;
	}

	.textareaBox {
		height: 360rpx;
		position: relative;
	}

	.textNum {
		position: absolute;
		width: 686rpx;
		font-size: 26rpx;
		color: #999;
		margin-left: 32rpx;
		margin-right: 32rpx;
		top: 10rpx;
		text-align: right;
	}

	.textareacontent .textareaInput {
		width: 686rpx;
		padding: 32rpx 32rpx 42rpx 32rpx;
		height: 286rpx;
		font-size: 34rpx;
		line-height: 65rpx;
		z-index: 0;
	}

	.pagemake {
		background-color: #000;
		opacity: 0.8;
		height: 100%;
		width: 100%;
		position: fixed;
		top: 0;
		/* #ifdef H5 */
		/* top: calc(88rpx + constant(safe-area-inset-top));
		top: calc(88rpx + env(safe-area-inset-top)); */
		/* #endif */
	}

	/* 评论框 -- end --*/
	.jiangqie-page-body {
		overflow: hidden;
	}

	button.jiangqie-page-laud-btn {
		box-sizing: content-box;
		line-height: inherit;
		line-height: 1.8rem;
	}

	.jiangqie-goback-btn {
		height: 96rpx;
		width: 96rpx;
		position: fixed;
		right: 40rpx;
		bottom: 200rpx;
	}

	.jiangqie-goback-btn button {
		background: none;
	}

	.jiangqie-goback-btn image {
		height: 96rpx;
		width: 96rpx;
	}

	.jiangqie-official-mod {
		padding: 10rpx 0;
	}

	.jiangqie-pre-next-view {
		display: flex;
		justify-content: space-between;
		padding: 0 30rpx 30rpx;
		border-bottom: 1rpx solid #EEEEEE;
		color: #333333;

		.jiangqie-pre-view,
		.jiangqie-next-view {
			display: flex;
			align-items: center;
		}
	}
</style>