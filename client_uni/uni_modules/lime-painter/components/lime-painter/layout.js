import { toPx, isNumber, getImageInfo  } from './utils'

export class Layout {
	constructor() {
	  this.layoutHeight = 0  
	  this.layoutWidth = 0  
	  this.uuid = 0;
	}
	init(context, root, isH5PathToBase64) {
		this.ctx = context
		this.root = root
		this.layoutWidth = root.width || 0
		this.layoutHeight = root.height || 0
		this.uuid = 0
		this.isH5PathToBase64 = isH5PathToBase64
	}
	async getNodeTree(element, parent = {}, index = 0, siblings = [], source) {
		let computedStyle = Object.assign({}, this.getComputedStyle(element, parent, index));
		let attributes = await this.getAttributes(element)
		let node = {
			id: this.uuid++,
			parent,
			computedStyle,
			rules: element.rules,
			attributes: Object.assign({}, attributes),
			name: element?.type || 'view',
			isRoot: !element?.type && JSON.stringify(parent) == '{}'
		}
		if(JSON.stringify(parent) === '{}' && !element.type) {
			const {left = 0, top = 0, width = 0, height = 0 } = computedStyle
			node.layoutBox = {left, top, width, height }
		} else {
			node.layoutBox = Object.assign({left: 0, top: 0}, this.getLayoutBox(node, parent, index, siblings, source))
		}
		if (element?.views) {
			let childrens = []
			node.children = []
			for (let i = 0; i < element.views.length; i++) {
				let v = element.views[i]
				childrens.push(await this.getNodeTree(v, node, i, childrens, element))
			}
			 node.children = childrens
		}
		return node
	}
	getComputedStyle(element, parent = {}, index = 0) {
		const style = {}
		const name = element.name || element.type
		const node = JSON.stringify(parent) == '{}' && !name ? element :  element.css;
		
		if(!node) return style
		const inheritProps = ['color', 'fontSize', 'lineHeight', 'verticalAlign', 'fontWeight', 'textAlign']
		if(parent.computedStyle) {
			inheritProps.forEach(prop => {
				if(node[prop] || parent.computedStyle[prop]) {
					node[prop] = node[prop] || parent.computedStyle[prop]
				}
			})
		}
		for (let value of Object.keys(node)) {
			const item = node[value]
			if(value == 'views') {continue}
			if (/^(box)?shadow$/i.test(value)) {
				let shadows = item.split(' ').map(v => /^\d/.test(v) ? toPx(v) : v)
				style.boxShadow = shadows
				continue
			}
			if (/^border/i.test(value) && !/radius$/i.test(value)) {
				const prefix = value.match(/^border([BTRLa-z]+)?/)[0]
				const type = value.match(/[W|S|C][a-z]+/)
				let v = item.replace(/([\(,])\s+|\s+([\),])/g,'$1$2').split(' ').map(v => /^\d/.test(v) ? toPx(v,'',true) : v)
				style[prefix] = {
					[prefix + 'Width'] : (isNumber(v[0]) ? v[0] : 0) || 1,
					[prefix + 'Style'] : v[1] || 'solid',
					[prefix + 'Color'] : v[2] || 'black'
				}
				if(v.length == 1 && type) {
					style[prefix][prefix + type[0]] = v[0]
				}
				continue
			}
			if (/^background(color)?$/i.test(value)) {
				style['backgroundColor'] = item
				continue
			}
			if(/padding|margin|radius/i.test(value)) {
				let isRadius = /radius$/i.test(value)
				let prefix = isRadius ? 'borderRadius' : value.match(/[a-z]+/)[0]
				let pre = [0,0,0,0].map((item, i) => isRadius ? ['borderTopLeftRadius', 'borderTopRightRadius', 'borderBottomRightRadius', 'borderBottomLeftRadius'][i] : [prefix + 'Top', prefix + 'Right', prefix + 'Bottom', prefix + 'Left'][i] )
				if(value === 'padding' || value === 'margin' || /^(border)?radius$/i.test(value)) {
					let v = item?.split(' ').map((item) => /^\d/.test(item) && toPx(item, node['width']), []) ||[0];
					let type = isRadius ? 'borderRadius' : value;
					let [t, r, b, l] = v
					style[type] = {
						[pre[0]]: t,
						[pre[1]]: isNumber(r) ? r : t,
						[pre[2]]: isNumber(b) ? b : t,
						[pre[3]]: isNumber(l) ? l : r || t
					}
				} else {
					if(typeof style[prefix] === 'object') {
						style[prefix][value] = toPx(item, node['width'])
					} else {
						style[prefix] = {
							[pre[0]]: style[prefix] || 0,
							[pre[1]]: style[prefix] || 0,
							[pre[2]]: style[prefix] || 0,
							[pre[3]]: style[prefix] || 0
						}
						style[prefix][value] = toPx(item, node['width'])
					}
				}
				continue
			}
			 if(/^(width|height)$/i.test(value)) {
				if(/%$/.test(item)) {
					style[value] = toPx(item, parent.layoutBox[value])
				} else {
					style[value] = /px|rpx$/.test(item) ? toPx(item) : item
				}
				continue
			}
			if(/^transform$/i.test(value)) {
				style[value]= {}
				item.replace(/([a-zA-Z]+)\(([0-9,-\.%rpxdeg\s]+)\)/g, (g1, g2, g3) => {
					const v = g3.split(',').map(k => k.replace(/(^\s*)|(\s*$)/g,''))
					const transform = (v, r) => {
						return v.includes('deg') ? v * 1 : r && !/%$/.test(r) ? toPx(v, r) : v
					}
					if(g2.includes('matrix')) {
						style[value][g2] = v.map(v => v * 1)
					} else if(g2.includes('rotate')) {
						style[value][g2] = g3.match(/^-?\d+(\.\d+)?/)[0] * 1
					}else if(/[X, Y]/.test(g2)) {
						style[value][g2] = /[X]/.test(g2) ? transform(v[0], node['width']) : transform(v[0], node['height'])
					} else {
						style[value][g2+'X'] = transform(v[0], node['width'] )
						style[value][g2+'Y'] = transform(v[1] || v[0], node['height'])
					}
				})
				continue
			}
			if(/^transformOrigin/i.test(value)) {
				style[value] = item
				continue
			}
			if(/%/.test(item)) {
				const {width: pw, height: ph, left: pl, top: pt} = parent.layoutBox;
				const {width: rw, height: rh} = this.root;
				const isR = style.position == 'relative'
				if(value == 'width') {
					style[value] = toPx(item, pw || rw)
				}else if(value == 'height') {
					style[value] = toPx(item, ph || rh)
				}else if(value == 'left') {
					style[value] = item // isR ? toPx(item, pw) + pl : toPx(item, rw)
				}else if(value == 'top') {
					style[value] = item // isR ? toPx(item, ph) + pt : toPx(item, rh)
				} else {
					style[value] = toPx(item, node['width'])
				}
			} else {
				style[value] = /px|rpx$/.test(item) ? toPx(item) : /em$/.test(item) && name == 'text'? toPx(item, node['fontSize']) : item
			}
		}
		if(/image/.test(element.name||element.type ) && !style.mode) {
			style.mode = element.mode || 'scaleToFill' 
			if((!node.width || node.width == 'auto') && (!node.height || node.width == 'auto') ) {
				style.mode = ''
			} 
		}
		return style
	}
	getLayoutBox(element, parent = {}, index = 0, siblings = [], source = {}) {
		let box = {}
		let {name, computedStyle: cstyle, layoutBox, attributes, isRoot} = element || {}
		if(!name) return box
		const {ctx} = this
		const { layoutBox: pbox = this.root, computedStyle: pstyle } = parent
		let { 
			verticalAlign: v, 
			left: x, 
			top: y,
			width: w,
			height: h,
			fontSize = 14,
			lineHeight = '1.4em',
			maxLines,
			fontWeight,
			fontFamily,
			textStyle,
			position,
			display,
			padding: p = {},
			margin: m = {},
			}  = cstyle;
		const { paddingTop: pt = 0, paddingRight: pr = 0, paddingBottom: pb = 0, paddingLeft: pl = 0, } = p
		const { marginTop: mt = 0, marginRight: mr = 0, marginBottom: mb = 0, marginLeft: ml = 0, } = m
		const {layoutBox: lbox, computedStyle: ls, name: lname} = siblings[index - 1] || {}
		const {layoutBox: rbox, computedStyle: rs, name: rname} = siblings[index + 1] || {}
		
		const lmb = ls?.margin?.marginBottom || 0
		const lmr = ls?.margin?.marginRight || 0
		
		if(/%$/.test(x)) {
			x = toPx(x, pbox.width)
		}
		if(/%$/.test(y)) {
			y = toPx(y, pbox.height)
		}
		if(position == 'relative') {
			x += pbox.left
			y += pbox.top
		}
		if(name === 'text') {
			const text = attributes.text ||''
			lineHeight = toPx(lineHeight, fontSize)
			ctx.save()
			ctx.setFonts({fontFamily, fontSize, fontWeight, textStyle})
			const isLeft = index == 0 
			const islineBlock = display === 'inlineBlock'
			const isblock = display === 'block' || ls?.display === 'block' 
			const isOnly = isLeft && !rbox || !parent?.id
			const lboxR = isLeft || isblock ? 0 : lbox.offsetRight || 0
			let texts = text.split('\n')
			let lineIndex = 1
			let line = ''
			const textIndent = cstyle.textIndent || 0
			if(!isOnly && !islineBlock) {
				texts.map((t, i) => {
					lineIndex += i
					const chars = t.split('')
					for (let j = 0; j < chars.length; j++) {
						let ch = chars[j]
						let textline = line + ch
						let textWidth = ctx.measureText(textline, fontSize).width
						if(lineIndex == 1) {
							textWidth = textWidth + (isblock ? 0 : lboxR) +  textIndent
						}
						if(textWidth > (w || pbox.width)) {
							lineIndex++
							line = ch
						} else {
							line = textline
						}
					}
				})
			} else {
				line = text
				lineIndex = Math.max(texts.length, Math.ceil(ctx.measureText(text, fontSize).width / ((w || pbox.width) - ctx.measureText('!', fontSize).width / 2)))
			}
			if(!islineBlock) {
				box.offsetLeft =  (isNumber(x) || isblock || isOnly ? textIndent : lboxR) +  pl + ml;
			}
			box.offsetTop = pt
			// 剩下的字宽度
			const remain = ctx.measureText(line, fontSize).width;
			let width =  (lineIndex > 1 ? pbox.width : remain + (box?.offsetLeft || 0)) + pr;
			if(!islineBlock) {
				box.offsetRight = (x || 0) + box.offsetLeft + (w ? w : (isblock ? pbox.width : remain)) + pr + mr;
			}
			const lboxOffset = lbox ? lbox.left + lbox.width : 0;
			const _getLeft = () => {
				if(islineBlock) {
					return (lboxOffset +  width > pbox.width || isLeft ? pbox.left : lboxOffset + lmr ) + ml
				}
				return (x || pbox.left)
			}
			const _getWidth = () => {
				if(islineBlock) {
					return width + pl + pr 
				}
				return w || (!isOnly || isblock ? pbox.width : (width > pbox.width - box.left || lineIndex > 1 ?  pbox.width - box.left : width))
			}
			const _getHeight = () => {
				if(h) {
					return h
				} else if(lineIndex > 1 ) {
					return (maxLines || lineIndex) * lineHeight + pt + pb 
				} else {
					return lineHeight + pt + pb 
				}
			}
			const _getTop = () => {
				let _y = y
				if(_y) {} else if(isLeft) {
					_y = pbox.top
				} else if(
					(lineIndex == 1 && width < pbox.width && lname === 'text' && !isblock && !islineBlock) || 
					lbox.width < pbox.width && !(islineBlock && (lboxOffset +  width > pbox.width))
				) {
					_y = lbox.top
				} else {
					_y = lbox.top + lbox.height  - (parent?.id && (ls?.lineHeight || 0))
				}
				if (v === 'bottom') {
					_y = pbox.top + (pbox.height - box.height || 0)
				}
				if (v === 'middle') {
					_y = pbox.top + (pbox.height ? (pbox.height - box.height || 0) / 2 : 0)
				}
				return _y + mt + (isblock && ls?.lineHeight || 0 ) + (lboxOffset +  width > pbox.width ? lmb : 0)
			}
			box.left = _getLeft() 
			box.width = _getWidth() 
			box.height = _getHeight()
			box.top = _getTop()
			if(pstyle && !pstyle.height) {
				pbox.height = box.top - pbox.top + box.height
			}
			ctx.restore()
		} else if(['view', 'qrcode'].includes(name)) {
			box.left = (x || pbox.left) + ml - mr
			box.width = (w || pbox?.width) - pl - pr
			box.height = (h || 0 ) 
			if(isNumber(y)) {
				box.top = y + mt
			} else {
				box.top = (lbox && (lbox.top + lbox.height) || pbox.top) + mt + lmb
			}
		} else if(name === 'image') {
			const {
				width: rWidth,
				height: rHeight
			} = attributes
			const limageOffset = lbox && (lbox.left + lbox.width)
			if(isNumber(x)) {
				box.left = x + ml - mr
			} else {
				box.left = (lbox && (limageOffset < pbox.width ? limageOffset : pbox.left) || pbox.left) + ml - mr
			}
			if(isNumber(w)) {
				box.width = w // - pl - pr 
			} else {
				box.width = Math.round(isNumber(h) ? rWidth * h / rHeight : pbox?.width) // - pl - pr
			}
			if(isNumber(h)) {
				box.height = h
			} else {
				const cH = Math.round(box.width * rHeight / rWidth )
				box.height = Math.min(cH, pbox?.height)
			}
			if(isNumber(y)) {
				box.top = y + mt
			} else {
				box.top = (lbox && (limageOffset < pbox.width ? limageOffset : (lbox.top + lbox.height)) || pbox.top) + mt + lmb
			}
		}
		
		if(box.top + box.height + mb > this.layoutHeight) {
			this.layoutHeight = box.top + box.height + mb
		}
		return box
	}
	async getAttributes(element) {
		let arr = { }
		if(element?.url || element?.src) {
			arr.src = element.url || element?.src;
			const {width = 0, height = 0, path: src, url} = await getImageInfo(arr.src, this.isH5PathToBase64) || {}
			arr = Object.assign({}, arr, {width, height, src, url})
		}
		if(element?.text) {
			arr.text = element.text
		}
		return arr
	}
	async calcNode(element) {
		return await this.getNodeTree(element)
	}
}