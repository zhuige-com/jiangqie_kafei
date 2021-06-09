<template>
	<canvas v-if="use2dCanvas" :id="canvasId" type="2d" :style="size + ';' + customStyle"></canvas>
	<canvas v-else :canvas-id="canvasId" :style="size + ';' + customStyle" :id="canvasId" :width="boardWidth * dpr" :height="boardHeight * dpr"></canvas>
</template>


<script>
import { toPx, compareVersion, sleep, isNumber, base64ToPath, pathToBase64 } from './utils';
import { Draw } from './draw';
import { Layout } from './layout';
import { adaptor, expand } from './canvas';
export default {
	// version: '1.6.1',
	name: 'l-painter',
	props: {
		board: Object,
		pathType: {
			type: String,
			//'base64'、'url'
		},
		fileType: {
			type: String,
			default: 'png'
		},
		quality: {
			type: Number,
			default: 1
		},
		width: [Number, String],
		height: [Number, String],
		pixelRatio: Number,
		customStyle: String,
		isRenderImage: Boolean,
		isH5PathToBase64: Boolean,
		sleep: {
			type: Number,
			default: 1000 / 60
		},
		beforeDelay: {
			type: Number,
			default: 1000 / 60
		},
		afterDelay: Number,
		// #ifdef MP-WEIXIN
		type: {
			type: String,
			default: '2d'
		}
		// #endif
	},
	data() {
		return {
			// #ifndef MP-WEIXIN || MP-QQ || MP-BAIDU
			canvasId: `l-painter${this._uid}`,
			// #endif
			// #ifdef MP-WEIXIN || MP-QQ || MP-BAIDU
			canvasId: `l-painter`,
			// #endif
			// #ifdef MP-WEIXIN
			use2dCanvas: true,
			// #endif
			// #ifndef MP-WEIXIN
			use2dCanvas: false,
			// #endif
			draw: null,
			ctx: null,
			canvasHeight: 0,
			canvasWidth: 0,
			layout: new Layout(),
			isDrawIng: false
		};
	},
	computed: {
		size() {
			return `width:${this.boardWidth}px; height: ${this.boardHeight}px;`;
		},
		dpr() {
			return this.pixelRatio || uni.getSystemInfoSync().pixelRatio;
		},
		boardWidth() {
			const { width: bw } = this.board || {};
			return toPx(this.width || bw);
		},
		boardHeight() {
			const { height: bh } = this.board || {};
			const { height: h, canvasHeight: ch } = this;
			return /^auto$/.test(h || bh) ? ch || 1 : toPx(h || bh);
		}
	},
	watch: {
		size() {
			// #ifdef MP-WEIXIN
			if (this.use2dCanvas) {
				this.inited = false;
			}
			// #endif
			// #ifdef MP-ALIPAY
			this.inited = false;
			// #endif
		}
	},
	// #ifdef MP-WEIXIN
	created() {
		const { SDKVersion, version, platform, environment } = wx.getSystemInfoSync();
		// ios wx7.0.20 createImage bug
		this.use2dCanvas = this.type === '2d' && compareVersion(SDKVersion, '2.9.2') >= 0 && !((/ios/i.test(platform) && /7.0.20/.test(version)) || /wxwork/i.test(environment));
	},
	// #endif
	mounted() {
		this.$watch(
			'board',
			async (val, old) => {
				if (JSON.stringify(val) === '{}' || !val) return;
				if (this.beforeDelay) {await sleep(this.beforeDelay)}
				this.render();
			},
			{
				deep: true,
				immediate: true
			}
		);
	},
	methods: {
		async update(args, single) {
			this.draw = null;
			// #ifdef MP-WEIXIN
			if (this.use2dCanvas) {
				this.ctx = null;
				this.inited = false;
			}
			// #endif
			// #ifdef MP-ALIPAY
			this.inited = false;
			// #endif
			this.isDrawIng = false;
			await new Promise(resolve => this.$nextTick(resolve));
			await sleep(200);
			return this.render(args, single);
		},
		async render(args = {}, single = false) {
			if (this.isDrawIng) {
				return this.update(args, single);
			}
			this.isDrawIng = true;
			const isArg = JSON.stringify(args) != '{}';
			const ctx = await this.getContext();
			let { use2dCanvas, boardWidth, boardHeight, board, canvas, isH5PathToBase64, sleep: s, afterDelay } = this;
			if (use2dCanvas && !canvas) {
				return Promise.reject(new Error('render: fail canvas has not been created'));
			}
			this.boundary = {
				top: 0,
				left: 0,
				width: boardWidth,
				height: boardHeight
			};
			if (!single) {
				ctx.clearRect(0, 0, boardWidth, boardHeight);
			}
			if (!this.draw || isArg) {
				this.draw = new Draw(ctx, canvas, use2dCanvas, isH5PathToBase64, s);
			}
			this.layout.init(ctx, this.boundary, isH5PathToBase64);
			if (isArg || (board && JSON.stringify(board) != '{}')) {
				this.node = await this.layout.calcNode(isArg ? args : Object.assign({}, board, { height: !board.type && board.height == 'auto' ? boardHeight : board.height }));
			}
			const { layoutHeight } = this.layout;
			if (/auto/.test(this.height || board.height) && !this.canvasHeight) {
				this.canvasHeight = layoutHeight;
				return this.update(args, single);
			}
			if (this.node) {
				await this.draw?.drawNode(this.node);
			}
			await new Promise(resolve => this.$nextTick(resolve));
			if (!use2dCanvas && !single) {
				await this.canvasDraw(ctx);
			}
			if (afterDelay) {
				await sleep(afterDelay);
			}
			this.$emit('done');
			if (this.isRenderImage && !single && this.isDrawIng) {
				this.canvasToTempFilePath()
					.then(async res => {
						if(this.pathType == 'base64') {
							if(/^data:image\/(\w+);base64/.test(res.tempFilePath)) {
								this.$emit('success', res.tempFilePath);
							} else {
								const img = await pathToBase64(res.tempFilePath)
								this.$emit('success', img)
							}
						} else if(this.pathType == 'url'){
							const img = await base64ToPath(res.tempFilePath)
							this.$emit('success', img);
						} else {
							this.$emit('success', res.tempFilePath);
						}
					})
					.catch(err => {
						this.$emit('fail', new Error(JSON.stringify(err)));
					});
			}
			this.isDrawIng = false;
			return Promise.resolve({ ctx, draw: this.draw, node: this.node });
		},
		async custom(cb) {
			const { ctx, draw } = await this.render({}, true);
			ctx.save();
			await cb(ctx, draw);
			ctx.restore();
			return Promise.resolve(true);
		},
		async single(args = {}) {
			const res = await this.render(args, true);
			return Promise.resolve(res);
		},
		canvasDraw(flag = false) {
			const { ctx } = this;
			return new Promise(resolve => {
				ctx.draw(flag, resolve);
			});
		},
		async getContext() {
			if (this.ctx && this.inited) {
				return Promise.resolve(this.ctx);
			}
			const { type, use2dCanvas, dpr, boardWidth, boardHeight } = this;
			const _getContext = () => {
				return new Promise(resolve => {
					uni.createSelectorQuery()
						.in(this)
						.select(`#${this.canvasId}`)
						.boundingClientRect()
						.exec(res => {
							if (res) {
								const ctx = uni.createCanvasContext(this.canvasId, this);
								if (!this.inited) {
									this.inited = true;
									this.use2dCanvas = false;
									this.canvas = res;
								}
								// #ifdef MP-ALIPAY
								ctx.scale(dpr, dpr);
								// #endif
								this.ctx = expand(ctx);
								resolve(this.ctx);
							}
						});
				});
			};
			// #ifndef MP-WEIXIN
			return _getContext();
			// #endif

			if (!use2dCanvas) {
				return _getContext();
			}
			return new Promise(resolve => {
				uni.createSelectorQuery()
					.in(this)
					.select(`#${this.canvasId}`)
					.node()
					.exec(res => {
						const canvas = res[0].node;
						if (!canvas) {
							this.use2dCanvas = false;
							resolve(this.getContext());
						}
						const ctx = canvas.getContext(type);
						if (!this.inited) {
							this.inited = true;
							canvas.width = boardWidth * dpr;
							canvas.height = boardHeight * dpr;
							this.use2dCanvas = true;
							this.canvas = canvas;
							ctx.scale(dpr, dpr);
						}
						this.ctx = adaptor(ctx);
						resolve(this.ctx);
					});
			});
		},
		canvasToTempFilePath(args = {}) {
			const { use2dCanvas, canvasId, dpr } = this;
			return new Promise((resolve, reject) => {
				let { top: y = 0, left: x = 0, width, height } = this.boundary || this;
				let destWidth = width * dpr;
				let destHeight = height * dpr;
				// #ifdef MP-ALIPAY
				width = destWidth;
				height = destHeight;
				// #endif
				let { fileType, quality } = args || this;
				const copyArgs = {
					x,
					y,
					width,
					height,
					destWidth,
					destHeight,
					canvasId,
					fileType,
					quality,
					success: resolve,
					fail: reject
				};
				if (use2dCanvas) {
					delete copyArgs.canvasId;
					copyArgs.canvas = this.canvas;
				}
				uni.canvasToTempFilePath(copyArgs, this);
			});
		}
	}
};
</script>
