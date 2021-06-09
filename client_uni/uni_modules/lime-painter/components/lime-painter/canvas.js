const _expand = ctx => {
	return {
		setFonts({fontFamily: ff = 'sans-serif', fontSize: fs = 14, fontWeight: fw = 'normal' , textStyle: ts = 'normal'}) {
			// 设置属性
			// #ifdef MP-TOUTIAO
			fw = fw == 'bold' ? 'bold' : ''
			ts =  ts == 'italic' ? 'italic' : ''
			// #endif
			ctx.font = `${ts} ${fw} ${fs}px ${ff}`;
		},
	}
};
export function expand(ctx) {
	return Object.assign(ctx, _expand(ctx))
}
export function adaptor(ctx) {
	return Object.assign(ctx, _expand(ctx), {
		setStrokeStyle(val) {
			ctx.strokeStyle = val;
		},
		setLineWidth(val) {
			ctx.lineWidth = val;
		},
		setLineCap(val) {
			ctx.lineCap = val;
		},
		setFillStyle(val) {
			ctx.fillStyle = val;
		},
		setFontSize(val) {
			ctx.font = String(val);
		},
		setGlobalAlpha(val) {
			ctx.globalAlpha = val;
		},
		setLineJoin(val) {
			ctx.lineJoin = val;
		},
		setTextAlign(val) {
			ctx.textAlign = val;
		},
		setMiterLimit(val) {
			ctx.miterLimit = val;
		},
		setShadow(offsetX, offsetY, blur, color) {
			ctx.shadowOffsetX = offsetX;
			ctx.shadowOffsetY = offsetY;
			ctx.shadowBlur = blur;
			ctx.shadowColor = color;
		},
		setTextBaseline(val) {
			ctx.textBaseline = val;
		},
		createCircularGradient() {},
		draw() {},
	});
}