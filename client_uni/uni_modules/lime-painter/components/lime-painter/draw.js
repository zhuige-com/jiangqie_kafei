import { toPx, isNumber, getImageInfo  } from './utils'
import { GD } from './gradient'
import QR from './qrcode'

export class Draw {
	constructor(context, canvas, use2dCanvas = false, isH5PathToBase64 = false, sleep) {
		this.ctx = context
		this.canvas = canvas || null
		this.use2dCanvas = use2dCanvas
		this.isH5PathToBase64 = isH5PathToBase64
		this.sleep = sleep
	}
	roundRect(x, y, w, h, r, fill = false, stroke = false, ) {
		if (r < 0) return
		const {ctx} = this
		ctx.beginPath()
		if(!r) {
			ctx.rect(x, y, w, h)
		} else {
			let {
				borderTopLeftRadius: tl = r || 0,
				borderTopRightRadius: tr = r || 0,
				borderBottomRightRadius: br = r || 0,
				borderBottomLeftRadius: bl = r || 0
			} = r || {}
			// 右下角
			ctx.arc(x + w - br, y + h - br, br, 0, Math.PI * 0.5)
			ctx.lineTo(x + bl, y + h)
			// 左下角
			ctx.arc(x + bl, y + h - bl, bl, Math.PI * 0.5, Math.PI)
			ctx.lineTo(x, y + tl)
			// 左上角
			ctx.arc(x + tl, y + tl, tl, Math.PI, Math.PI * 1.5)
			ctx.lineTo(x + w - tr, y)
			// 右上角
			ctx.arc(x + w - tr, y + tr, tr, Math.PI * 1.5, Math.PI * 2)
			ctx.lineTo(x + w, y + h - br)
		}
		ctx.closePath()
		if (stroke) ctx.stroke()
		if (fill) ctx.fill()
	}
	setTransform(box, {transform, transformOrigin: o = 'center center'}) {
		const {ctx} = this
		let {
			scaleX = 1,
			scaleY = 1,
			translateX = 0,
			translateY = 0,
			rotate = 0,
			skewX = 0,
			skewY = 0
		} = transform || {}
		let {
			left: x,
			top: y,
			width: w,
			height: h
		} = box
		translateX = toPx(translateX, w) || 0
		translateY = toPx(translateY, h) || 0
		
		const yMaps = {
			"top": toPx('0%', 1),
			"center": toPx('50%', 1, true),
			"bottom":  toPx('100%', 1),
		}
		const xMaps = {
			"left": toPx('0%', 1),
			"center": toPx('50%', 1, true),
			"right": toPx('100%', 1)
		}
		o = o.split(' ').filter((v,i) => i < 2).reduce((c,v) => {
			if(/\d+/.test(v)) {
				let n = toPx(v, 1, true) / (/px|rpx$/.test(v) ? (isNumber(c.x) ? h : w) : 1)
				return isNumber(c.x) ? Object.assign(c, {y: n}) : Object.assign(c, {x: n})
			} else {
				return isNumber(xMaps[v]) && !isNumber(c.x) ? Object.assign(c, {x: xMaps[v]}): Object.assign(c, {y: yMaps[v]})
			}
		}, {})
		ctx.scale(scaleX, scaleY)
		const offset = {
			x: w * (scaleX > 0 ? 1 : -1) * o.x + (x + translateX) / scaleX, 
			y: h * (scaleY > 0 ? 1 : -1) * o.y + (y + translateY) / scaleY
		}
		ctx.translate(offset.x, offset.y) 
		if(rotate) {
			ctx.rotate(rotate * Math.PI / 180)
		}
		if(skewX || skewY) {
			ctx.transform(1, Math.tan(skewY * Math.PI/180), Math.tan(skewX * Math.PI/180), 1 , 0, 0)
		}
		return {x: -w * o.x, y: -h * o.y, w, h}
	}
	setBackground(bg , w, h) {
		const {ctx} = this
		if (!bg) {
			// #ifndef MP-TOUTIAO || MP-BAIDU
			ctx.setFillStyle('transparent')
			// #endif
			// #ifdef MP-TOUTIAO || MP-BAIDU
			ctx.setFillStyle('rgba(0,0,0,0)')
			// #endif
		} else if(GD.isGradient(bg)) {
			GD.doGradient(bg, w, h, ctx);
		} else {
			ctx.setFillStyle(bg)
		}
	}
	setShadow({boxShadow: bs = []}) {
		const {ctx} = this
		if (bs.length) {
			const [x, y, b, c] = bs
			ctx.setShadow(x, y, b, c)
		}
	}
	setBorder(box, style) {
		const {ctx} = this
		let {
			width: w,
			height: h
		} = box
		const {border, borderBottom, borderTop, borderRight, borderLeft, borderRadius: r} = style;
		const {
			borderWidth : bw = 0,
			borderStyle : bs,
			borderColor : bc,
		} = border || {}
		const {
			borderBottomWidth : bbw = bw,
			borderBottomStyle : bbs = bs,
			borderBottomColor : bbc= bc,
		} = borderBottom || {}
		const {
			borderTopWidth: btw = bw,
			borderTopStyle: bts = bs,
			borderTopColor: btc = bc,
		} = borderTop || {}
		const {
			borderRightWidth: brw = bw,
			borderRightStyle: brs = bs,
			borderRightColor: brc = bc,
		} = borderRight || {}
		const {
			borderLeftWidth: blw = bw,
			borderLeftStyle: bls = bs,
			borderLeftColor: blc  = bc,
		} = borderLeft || {}
		
		let {
			borderTopLeftRadius: tl = r || 0,
			borderTopRightRadius: tr = r || 0,
			borderBottomRightRadius: br = r || 0,
			borderBottomLeftRadius: bl = r || 0
		} = r || {r,r,r,r}
		if(!borderBottom && !borderLeft && !borderTop && !borderRight && !border) return;
		const _borderType = (w, s, c) => {
			if (s == 'dashed') {
				// #ifdef MP
				ctx.setLineDash([Math.ceil(w * 4 / 3), Math.ceil(w * 4 / 3)])
				// #endif
				// #ifndef MP
				ctx.setLineDash([Math.ceil(w * 6), Math.ceil(w * 6)])
				// #endif
			} else if (s == 'dotted') {
				ctx.setLineDash([w, w])
			}
			ctx.setStrokeStyle(c)
		}
		const _setBorder = (x1, y1, x2, y2, x3, y3, r1, r2, p1, p2, p3,  bw, bs, bc) => {
			ctx.save()
			// this.setOpacity(style)
			// this.setTransform(box, style)
			ctx.setLineWidth(bw)
			_borderType(bw, bs, bc)
			ctx.beginPath()
			ctx.arc(x1, y1, r1, Math.PI * p1, Math.PI * p2)
			ctx.lineTo(x2, y2)
			ctx.arc(x3, y3, r2, Math.PI * p2, Math.PI * p3)
			ctx.stroke()
			ctx.restore()
		}
		ctx.save()
		this.setOpacity(style)
		let {x, y} = this.setTransform(box, style)
		if(border) {
			ctx.setLineWidth(bw)
			_borderType(bw, bs, bc)
			this.roundRect(x, y, w, h, r, false, bc ? true : false)
			ctx.restore()
		}
		
		if(borderBottom) {
			_setBorder(x + w - br, y + h - br, x + bl, y + h, x + bl, y + h - bl, br, bl, 0.25, 0.5, 0.75, bbw, bbs, bbc)
		}
		if(borderLeft) {
			// 左下角
			_setBorder(x + bl, y + h - bl, x, y + tl, x + tl, y + tl, bl, tl, 0.75, 1, 1.25, blw, bls, blc)
		}
		if(borderTop) {
			// 左上角
			_setBorder(x + tl, y + tl, x + w - tr, y, x + w - tr, y + tr, tl, tr, 1.25, 1.5, 1.75, btw, bts, btc)
		}
		if(borderRight) {
			// 右上角
			_setBorder(x + w - tr, y + tr, x + w, y + h - br, x + w - br, y + h - br, tr, br, 1.75, 2, 0.25, btw, bts, btc)
		}
	}
	setOpacity({opacity = 1}) {
		this.ctx.setGlobalAlpha(opacity)
	}
	drawView(box, style) {
		const {ctx} = this
		const {
			width: w,
			height: h
		} = box
		let {
			borderRadius: br = 0,
			backgroundColor: bg,
		} = style || {}
		ctx.save()
		this.setOpacity(style)
		let {x, y} = this.setTransform(box, style)
		this.setShadow(style)
		this.setBackground(bg, w, h)
		this.roundRect(x, y, w, h, br, true, false)
		ctx.restore()
		this.setBorder(box, style)
	}
	async drawImage(img, box = {}, style = {}, custom = true) {
		await new Promise(async (resolve, reject) => {
			const {ctx, sleep} = this
			const canvas = this.canvas
			let {
				borderRadius = 0,
				mode,
				padding = {},
				backgroundColor: bg,
			} = style
			const {paddingTop: pt = 0, paddingLeft: pl= 0, paddingRight: pr= 0, paddingBottom: pb = 0} = padding;
			let {
				left: x,
				top: y,
				width: w,
				height: h
			} = box
			ctx.save()
			if(!custom) {
				this.setOpacity(style)
				let {x: x1, y: y1} = this.setTransform(box, style)
				if(bg) {
					this.setBackground(bg, w, h)
				}
				x = x1
				y = y1
				this.setShadow(style)
				this.roundRect(x, y, w, h, borderRadius, borderRadius ? true : false, false)
			}
			ctx.clip()
			const _modeImage = (img) => {
				x += pl
				y += pt
				w = w - pl - pr
				h = h - pt - pb
				// 获得图片原始大小
				let {width: rw, height: rh, src} = img
				let sX = 0
				let sY = 0
				// 绘画区域比例
				const cp = w / h
				// 原图比例
				const op = rw / rh
				if(!rw) {
					mode = 'scaleToFill'
				}
				switch(mode) {
					case 'aspectFit': 	
						if(cp >= op) {
							rw = h * op;
							rh = h
							sX = x + Math.round(w - rw) / 2 
							sY = y
						} else {
							rw = w
							rh = w / op;
							sX = x
							sY = y + Math.round(h - rh) / 2 
						}
						ctx.drawImage(src, sX, sY, rw, rh);
						break;
					case 'aspectFill': 
						if (cp >= op) {
							rh = rw / cp;
							// sY = Math.round((h - rh) / 2)
						} else {
							rw = rh * cp;
							sX = Math.round(((img.width || w) - rw) / 2)
						}
						// 百度小程序
						// #ifdef MP-BAIDU
						ctx.drawImage(src, x, y, w, h, sX, sY, rw, rh)
						// #endif
						// #ifndef MP-BAIDU
						ctx.drawImage(src, sX, sY, rw, rh, x, y, w, h)
						// #endif
						 break;
					default: 
						// scaleToFill
						ctx.drawImage(src, x, y, w, h);
				}
			}
			const _restore = () => {
				ctx.restore()
				this.setBorder(box, style)
				setTimeout(resolve, sleep)
			}
			const _drawImage = (img) => {
				if (this.use2dCanvas) {
					const Image = canvas.createImage()
					Image.onload = () => {
						img.src = Image
						_modeImage(img)
						_restore()
					}
					Image.onerror = async () => {
						if(img.url) {
							const {path: src, width, height} = await getImageInfo(img.url, this.isH5PathToBase64, true)
							_drawImage({src, width, height})
						} else {
							console.error(`createImage fail: ${JSON.stringify(img)}`)
							resolve(true)
						}
					}
					Image.src = img.src
				} else {
					_modeImage(img)
					_restore()
				}
			}
			if(typeof img === 'string') {
				const {path: src, width, height} = await getImageInfo(img, this.isH5PathToBase64)
				_drawImage({src, width, height})
			} else {
				_drawImage(img)
			}
		})
	}
	drawText(text, box, style, rules) {
		const {ctx} = this
		let {
			width: w,
			height: h,
			offsetLeft: ol = 0,
			offsetTop: ot = 0,
		} = box
		let {
			color = '#000000',
			lineHeight = '1.4em',
			fontSize = 14,
			fontWeight,
			fontFamily,
			textStyle,
			textAlign = 'left',
			verticalAlign: va = 'top',
			backgroundColor: bg,
			maxLines,
			display,
			padding = {},
			borderRadius = 0,
			textDecoration: td
		} = style
		lineHeight = toPx(lineHeight, fontSize)
		if (!text) return
		ctx.save()
		this.setOpacity(style)
		let {x, y} = this.setTransform(box, style)
		ctx.setTextBaseline(va)
		ctx.setFonts({fontFamily, fontSize, fontWeight, textStyle})
		ctx.setTextAlign(textAlign)
		
		if(bg) {
			this.setBackground(bg, w, h)
			this.roundRect(x, y, w, h, borderRadius, 1, 0)
		 }
		 y += ot
		this.setShadow(style)
		ctx.setFillStyle(color)
		let rulesObj = {};
		if(rules) {
			if (rules.word.length > 0) {
				for (let i = 0; i < rules.word.length; i++) {
					// let reg = new RegExp(rules.word[i], "g")
					// let result;
					// while(result=reg.exec(text)) {
					// 		rulesObj[result.index] = {
					// 			reset: true,
					// 			char: result[0]
					// 		};
					// }
					let startIndex = 0,
						index;
					while ((index = text.indexOf(rules.word[i], startIndex)) > -1) {
						rulesObj[index] = { 
							reset: true
						};
						for (let j = 0; j < rules.word[i].length; j++) {
							rulesObj[index + j] = { 
								reset: true
							};
						}
						startIndex = index + 1;
					}
				}
			}
		}
		// 水平布局
		switch (textAlign) {
			case 'left':
				break
			case 'center':
				x += 0.5 * w
				break
			case 'right':
				x += w
				break
			default:
				break
		}
		const textWidth = ctx.measureText(text).width
		const actualHeight = Math.ceil(textWidth / w) * lineHeight
		let paddingTop = Math.ceil((h - actualHeight) / 2)
		if (paddingTop < 0) paddingTop = 0
		// 垂直布局
		switch (va) {
			case 'top':
				break
			case 'middle':
				y += fontSize / 2
				break
			case 'bottom':
				y += fontSize 
				break
			default:
				break
		}
		
		// 绘线
		const _drawLine = (x, y, textWidth) => {
			const { system } = uni.getSystemInfoSync()
			if(/win|mac/.test(system)){
				y += (fontSize / 3)
			}
			// 垂直布局
			switch (va) {
				case 'top':
					break
				case 'middle':
					y -= fontSize / 2 
					break
				case 'bottom':
					y -= fontSize
					break
				default:
					break
			}
			let to = x
			switch (textAlign) {
				case 'left':
					x = x
					to+= textWidth
					break
				case 'center':
					x = x - textWidth / 2
					to = x + textWidth
					break
				case 'right':
					to = x
					x = x - textWidth
					break
				default:
					break
			}
			
			if(td) {
				ctx.setLineWidth(fontSize / 13);
				ctx.beginPath();
				
				if (/\bunderline\b/.test(td)) {
					y -= inlinePaddingTop * 0.8
					ctx.moveTo(x, y);
					ctx.lineTo(to, y);
				}
				
				if (/\boverline\b/.test(td)) {
					y += inlinePaddingTop
					ctx.moveTo(x, y - lineHeight);
					ctx.lineTo(to, y - lineHeight);
				}
				if (/\bline-through\b/.test(td)) {
					ctx.moveTo(x , y - lineHeight / 2 );
					ctx.lineTo(to, y - lineHeight /2 );
				}
				ctx.closePath();
				ctx.setStrokeStyle(color);
				ctx.stroke();
			}
		}
		const _reset = (text, x, y) => {
			const rs = Object.keys(rulesObj)
			for (let i = 0; i < rs.length; i++) {
				const item = rulesObj[rs[i]]
				ctx.save();
				ctx.setFillStyle(rules.color);
				if(item.char) {
					ctx.fillText(item.char, item.x , item.y)
				}
				ctx.restore();
			}
		}
		const _setText = (isReset, char) => {
			if(isReset) {
				const t1 = Math.round(ctx.measureText('\u0020').width)
				const t2 = Math.round(ctx.measureText('\u3000').width)
				const width = Math.round(ctx.measureText(char).width)
				let _char = ''
				let _num = 1
				if(width == t2){
					_char ='\u3000'
					_num = 1
				} else {
					_char = '\u0020'
					_num = Math.ceil(width / t1)
				}
				return {char: new Array(_num).fill(_char).join(''), width}
			} else {
				return {char}
			}
		}
		const _setRulesObj = (text, index, x, y) => {
			rulesObj[index].x = x
			rulesObj[index].y = y
			rulesObj[index].char = text
		}
		const _setRules = (x, rs, text, textWidth, {startIndex = 0, endIndex}) => {
			let clonetext = text
			if(/·/.test(text)) {
				clonetext = clonetext.replace(/·/g, '.')
				textWidth = ctx.measureText(clonetext).width
			}
			let _text = text.split('')
			let _x = x
			for (let i = 0; i < rs.length; i++) {
				const index = rs[i]
				const key = index - startIndex
				const t = _text[key]
				if(t) {
					let {char, width} = _setText(rulesObj[index], t)
					_text[key] = char
					if(textAlign == 'center') {
						_x = x - 0.5 * (textWidth - width)
					}
					if(textAlign == 'right') {
						 _x = x - textWidth + width
					}
					_setRulesObj(t, index, _x  + ctx.measureText(clonetext.substring(0, key)).width, y + inlinePaddingTop)
				} else {
					continue
				}
				
			}
			return _text
		}
		const inlinePaddingTop = Math.ceil((lineHeight - fontSize) / 2)
		// 不超过一行
		if (textWidth + ol <= w && !text.includes('\n')) {
			x = x + ol
			const rs = Object.keys(rulesObj)
			let _text = ''
			if(rs) {
				_text = _setRules(x, rs, text, textWidth, {})
				_reset()
			}
			ctx.fillText(_text.join(''), x, y + inlinePaddingTop)
			y += lineHeight
			_drawLine(x, y, textWidth)
			ctx.restore()
			this.setBorder(box, style)
			return
		}
		// 多行文本
		const chars = text.split('')
		const _y = y
		let _x = x
		// 逐行绘制
		let line = ''
		let lineIndex = 0
		let startIndex = 0
		for(let index = 0 ; index <= chars.length; index++){
			let ch = chars[index] || ''
			const isLine = ch === '\n'
			const isRight = ch == ''// index == chars.length
			ch = isLine ? '' : ch;
			let textline = line + ch
			let textWidth = ctx.measureText(textline).width
			// 绘制行数大于最大行数，则直接跳出循环
			if (lineIndex >= maxLines) {
				break;
			}
			if(lineIndex == 0) {
				textWidth = textWidth + ol
				_x = x + ol
			} else {
				textWidth = textWidth
				_x = x
			}
			
			if (textWidth > w || isLine || isRight) {
				let endIndex = index
				lineIndex++
				line = isRight && textWidth <= w ? textline : line
				if(lineIndex === maxLines && textWidth > w) {
					while( ctx.measureText(`${line}...`).width > w) {
						if (line.length <= 1) {
							// 如果只有一个字符时，直接跳出循环
							break;
						}
						line = line.substring(0, line.length - 1);
					}
					line += '...'
				}
				const rs = Object.keys(rulesObj)
				let _text = ''
				if(rs) {
					_text = _setRules(x, rs, line, textWidth, {startIndex, endIndex})
					_reset()
				}
				ctx.fillText(_text.join(''), _x, y + inlinePaddingTop)
				y += lineHeight
				_drawLine(_x, y, textWidth)
				line = ch
				startIndex = endIndex + (isLine ? 1 : 0)
				if ((y + lineHeight) > (_y + h)) break
			} else {
				line = textline
			}
		}
		ctx.restore()
		this.setBorder(box, style)
	}
	async drawNode(element) {
		const {
			layoutBox: box,
			computedStyle: style,
			attributes: attr,
			name,
			rules,
			children
		} = element
		const {
			src,
			text
		} = element.attributes
		if (name === 'view') {
			this.drawView(box, style)
		} else if (name === 'image' && src) {
			await this.drawImage(attr, box, style, false)
		} else if (name === 'text') {
			this.drawText(text, box, style, rules)
		} else if (name === 'qrcode') {
			if(QR?.api) {
				QR.api.draw(text, this, box, style)
			}
		}
		if (!children) return
		const childs = Object.values ? Object.values(children) : Object.keys(children).map((key) => children[key]);
		for (const child of childs) {
			await this.drawNode(child)
		}
	}
}