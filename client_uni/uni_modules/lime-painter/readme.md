# Painter 画板
> uniapp 海报画板，可根据自身需求配置生成海报
> [查看更多](http://liangei.gitee.io/limeui-docs/components/painter/) <br>
> Q群：458377637 <br>
> 群里有最新Demo
<br>


### 平台兼容

| H5  | 微信小程序 | 支付宝小程序 | 百度小程序 | 头条小程序 | QQ 小程序 | App |
| --- | ---------- | ------------ | ---------- | ---------- | --------- | --- |
| √   | √          | √         | 未测       | √          | √      | √   |

<br>
<br>

### 引入

```js
// 非uni_modules引入方式
import lPainter from '@/components/lime-painter/'
// uni_modules引入方式
import lPainter from '@/uni_modules/lime-painter/components/lime-painter/'
export default {
	components: {lPainter}
}
```

<br>
<br>

### 代码演示
#### 基本用法

定一个画板对象，包含`width`、`height`、`background`，`views`为画板里的元素集，它是一个数组对象。<br>
元素类型目前有`view`、`text`、`image`<br>
css 对象里的位置都是相对于画板的绝对定位，支持`rpx`、`px`

```html
<l-painter :board="base"/>
```

```js
export default {
    data() {
        return {
            base: {
                width: '686rpx',
				height: '130rpx',
				views: [
					{
						type: 'view',
						css: {
							left: '0rpx',
							top: '0rpx',
							background: '#07c160',
							width: '120rpx',
							height: '120rpx'
						}
					},
                    {
						type: 'view',
						css: {
							left: '180rpx',
							top: '18rpx',
							background: '#1989fa',
							width: '80rpx',
							height: '80rpx',
							transform: 'transform: rotate(50deg)'
						}
					}
				]
            }
        }
    }
}

```

<br><br>

#### 圆角

为可元素定一个圆角`radius`,支持`rpx`、`px`、`%`

```html
<l-painter :board="base"/>
```

```js
export default {
data() {
    return {
        base: {
            width: '686rpx',
            height: '130rpx',
            views: [
                {
                    type: 'view',
                    css: {
                        left: '0rpx',
                        top: '0rpx',
                        background: '#07c160',
                        width: '120rpx',
                        height: '120rpx',
                        radius: '16rpx 30rpx 10rpx 80rpx'
                    }
                },
                {
                    type: 'view',
                    css: {
                        left: '150rpx',
                        top: '0rpx',
                        background: '#1989fa',
                        width: '120rpx',
                        height: '120rpx',
                        radius: '16rpx 60rpx'
                    }
                },
                {
                    type: 'view',
                    css: {
                        left: '300rpx',
                        top: '0rpx',
                        background: '#ff976a',
                        width: '120rpx',
                        height: '120rpx',
                        radius: '50%'
                    }
                }
            ]
        }
    }
}
}

```

<br><br>

#### 描边投影

为可元素定一个描边`border`和投影`shadow`,支持`rpx`、`px`

```html
<l-painter :board="base"/>
```

```js
export default {
data() {
    return {
        base: {
            width: '686rpx',
            height: '130rpx',
            views: [
                {
                    type: 'view',
                    css: {
                        left: '10rpx',
                        top: '10rpx',
                        background: 'rgba(7,193,96,.1)',
                        width: '120rpx',
                        height: '120rpx',
                        radius: '50%',
                        border: '2rpx dashed #07C160'
                    }
                },
                {
                    type: 'view',
                    css: {
                        left: '150rpx',
                        top: '10rpx',
                        background: 'rgba(25,137,250,.4)',
                        width: '120rpx',
                        height: '120rpx',
                        radius: '50%',
                        shadow: '0 5rpx 10rpx rgba(25,137,250,.8)'
                    }
                },
                {
                    type: 'view',
                    css: {
                        left: '300rpx',
                        top: '10rpx',
                        background: 'rgba(255, 151, 106, .1)',
                        width: '120rpx',
                        height: '120rpx',
                        radius: '50%',
                        border: '2rpx solid #ff976a'
                    }
                }
            ]
        }
    }
}
}

```

<br><br>

#### 图片

元素类型为`image`时，需要一个图片地址`src`,图片地址支持`相对路径`和`网络地址`<br>

::: warning 温馨提示
当为网络地址时，<br>
H5：需要解决跨域问题或使用`isH5PathToBase64`可解决部分问题。 <br>
小程序：需要配置 downloadFile 域名 <br>
本插件全端支持 base64 图片 <br>
:::
<br>

图片模式`mode`目前支持的有`aspectFill`、`aspectFit`、`scaleToFill`,默认为`scaleToFill`,`height`和`width`支持`auto`，当设置为`auto`时`mode`失效。


```html
<l-painter :board="base"/>
```

```js
export default {
data() {
    return {
        base: {
            width: '686rpx',
            height: '130rpx',
            views: [
                {
                    type: 'image',
                    src: '../../static/avatar-1.jpeg',
                    css: {
                        left: '0rpx',
                        top: '0rpx',
                        width: '120rpx',
                        height: '120rpx'
                    }
                },
                {
                    type: 'image',
                    src: '../../static/avatar-2.jpg',
                    css: {
                        left: '150rpx',
                        top: '0rpx',
                        width: '120rpx',
                        height: '120rpx',
                        radius: '16rpx'
                    }
                },
                {
                    type: 'image',
                    src:'../../static/avatar-3.jpeg',
                    css: {
                        left: '300rpx',
                        top: '0rpx',
                        background: '#ff976a',
                        width: '120rpx',
                        height: '120rpx',
                        radius: '50%'
                    }
                }
            ]
        }
    }
}
}

```

<br><br>

#### 文字

元素类型`text`时，内容写在`text`里，支持`\n`换行符，css 的属性有字体大小`fontSize`、行高`lineHeight`、对齐`textAlign`、修饰`textDecoration`、粗细`fontWeight`、 宽度`width`、最大行数`maxLines`。
设置了最大行数和宽度时，当文字内容超过会显示省略号。

```html
<l-painter :board="base"/>
```

```js
export default {
data() {
    return {
        base: {
            width: '686rpx',
            height: '500rpx',
            views: [
                {
                    type: 'text',
                    text: '左对齐,下划线\n无风才到地，有风还满空\n缘渠偏似雪，莫近鬓毛生',
					// 可以指定关键字颜色
					rules: {
						word: ['到地'],
						color: 'red'
					},
                    css: {
                        left: '0rpx',
                        top: '10rpx',
                        fontSize: '28rpx',
                        lineHeight: '36rpx',
                        textDecoration: 'underline'
                    }
                },
                {
                    type: 'text',
                    text: '居中,上划线\n无风才到地，有风还满空\n缘渠偏似雪，莫近鬓毛生',
                    css: {
                        left: '0rpx',
                        top: '150rpx',
                        fontSize: '28rpx',
                        lineHeight: '36rpx',
                        textAlign: 'center',
                        textDecoration: 'overline'
                    }
                },
                {
                    type: 'text',
                    text: '右对齐,中划线\n无风才到地，有风还满空\n缘渠偏似雪，莫近鬓毛生',
                    css: {
                        left: '0rpx',
                        top: '290rpx',
                        fontSize: '28rpx',
                        lineHeight: '36rpx',
                        textAlign: 'right',
                        textDecoration: 'line-through',
                    }
                },
                {
                    type: 'text',
                    text: '省略号\n明月几时有？把酒问青天。不知天上宫阙，今夕是何年。我欲乘风归去，又恐琼楼玉宇，高处不胜寒。起舞弄清影，何似在人间。',
                    rules: {
                    	word: ['几时有'],
                    	color: 'red'
                    },
					css: {
                        left: '0rpx',
                        top: '420rpx',
                        fontSize: '28rpx',
                        maxLines: 2,
                        width: '686rpx',
                        lineHeight: '36rpx'
                    }
                }
            ]
        }
    }
}
}

```
<br><br>

#### 二维码
该方法需要[下载此链接的文件](https://gitee.com/liangei/lime-painter/blob/master/qrcode.js)覆盖插件目录的`qrcode.js`才生效。<br>
考虑到不是很多人需要，文件又比较大。故出此下策。
```html
<l-painter :board="base" />
```
```js
{
	type: 'qrcode',
	text: 'https://www.baidu.com',
	css: {
		left: '380rpx',
		top: '230rpx',
		width: '200rpx',
		height: '200rpx',
		color: '#1989fa',
		backgroundColor: 'rgba(25,137,250,.1)',
		border: '20rpx solid rgba(25,137,250,.1)',
	}
}

```


<br>

#### 调用内部方法
插件也允许通过`ref`获取内部方法来实现绘制和生成图片。
需要设置画板的`width`和`height`
```html
<l-painter ref="painter" width="686rpx"  height="500rpx" />
<image :src="path" />
```

```js
export default {
	data() {
		return {
			base: {
				width: '686rpx',
				height: '500rpx',
				views: [
					{
						type: 'view',
						css: {
							fontSize: '28rpx',
							lineHeight: '1.4em',
						},
						views: [
							{
								type: 'text',
								text: '水调歌头',
								css: {
									display: 'block',
									fontSize: '42rpx',
									textAlign: 'center'
								}
							},
							{
								type: 'text',
								text: '丙辰中秋，欢饮达旦，大醉，作此篇，兼怀子由。',
								css: {
									display: 'block',
									textIndent: '2em'
								}
							},
							{
								type: 'text',
								text: '明月几时有？把酒问青天。不知天上宫阙，今夕是何年？我欲乘风归去，又恐琼楼玉宇，高处不胜寒。起舞弄清影，何似在人间',
								css: {
									display: 'block',
									color: 'red',
									textIndent: '2em'
								}
							},
							{
								type: 'text',
								text: '转朱阁，低绮户，照无眠。不应有恨，何事长向别时圆？人有悲欢离合，月有阴晴圆缺，此事古难全。但愿人长久，千里共婵娟',
								css: {
									textIndent: '2em'
								}
							}
						]
					}
				]
			},
			path: '',
		}
	},
	methods: {
		onRender() {
			// 支持通过调用render传入参数
			const painter = this.$refs.painter
			painter.render(this.base)
		},
		canvasToTempFilePath() {
			const painter = this.$refs.painter
			// 支持通过调用canvasToTempFilePath方法传入参数 调取生成图片
			painter.canvasToTempFilePath().then(res => this.path = res.tempFilePath)
		}
	}
}
```
<br>

#### 自定义
有时画板提供的方法无法满足复杂个性的需要，可通过插件`ref`获取`ctx`来使用原生方法绘制。
```html
<l-painter ref="painter" width="686rpx"  height="500rpx" />
<image :src="path" />
```

```js
export default {
	data() {
		return {
			path: '',
		}
	},
	methods: {
		async onCustom() {
			const p = this.$refs.custom
			// 自定义，使用原生和内部方法生成更个性的图形
			await p.custom(async (ctx, draw) => {
				// 原生方法的单位为px，如果想用rpx,请使用uni.upx2px(150)
				// 绘制背景颜色
				ctx.setFillStyle('#ff976a')
				ctx.setShadow(5, 5, 50, '#ff976a')
				ctx.beginPath()
				ctx.arc(150, 150, 20, Math.PI, Math.PI * 1.5)
				ctx.lineTo(150, 20)
				ctx.arc(150, 0, 20, Math.PI * 0.5, Math.PI * 1)
				ctx.lineTo(5, 0)
				ctx.arc(0, 0, 20, Math.PI * 0, Math.PI * 0.5)
				ctx.lineTo(0, 130)
				ctx.arc(0, 150, 20, Math.PI * 1.5, Math.PI * 2)
				ctx.lineTo(130, 150)
				ctx.closePath()
				ctx.fill()
				// 绘制好形状后，调用draw的drawImage方法填充图片，图片会下载完成后绘制。无需自己下载。
				await draw.drawImage('../../static/avatar-1.jpeg', {left: 0, top: 0, width:150, height:150})
			})
			await p.custom((ctx) => {
				// 绘制背景颜色
				ctx.setFillStyle('#07c160')
				ctx.beginPath()
				ctx.arc(280, 50, 50, 0, Math.PI * 2)
				ctx.closePath()
				ctx.fill()
			})
			// 单独绘制单个或多个元素的内部方法，
			// 多个是是数组，单个是为对象
			// 绘制二维码，由于二维码JS文件较大，也不是所有人需要。所以需自行下载覆盖。
			await p.single({
				type: 'qrcode',
				text: 'https://www.baidu.com',
				css: {
					left: '380rpx',
					top: '230rpx',
					width: '200rpx',
					height: '200rpx',
					color: '#1989fa',
					backgroundColor: 'rgba(25,137,250,.1)',
					border: '20rpx solid rgba(25,137,250,.1)',
					transform: 'rotate(45deg)'
				}
			})
			await p.single({
				type: 'text',
				text: '床前明月光，疑是地上霜。\n举头望明月，低头思故乡。',
				css: {
					left: '0rpx',
					top: '330rpx'
				}
			})
			// 使用自定义时，要配合方法绘制，否则无法绘制。
			await p.canvasDraw(true)
		},
		// 支持通过调用canvasToTempFilePath方法传入参数 调取生成图片
		onCanvasToTempFilePath() {
			const painter = this.$refs.custom
			painter.canvasToTempFilePath({dpr:2}).then(res => this.path = res.tempFilePath)
			
		}
	}
}
```

<br><br>

#### 提供一份海报样式案例

是否生成图片:`isRenderImage` <br>
生成图片成功:`success`返回一个图片临时地址。

```html
<l-painter
  isRenderImage
  custom-style="position: fixed; left: 200%;"
  :board="base"
  @success="path = $event"
/>
```

```js
export default {
data() {
    return {
        base: {
            width: '750rpx',
            height: '1114rpx',
            background: '#F6F7FB',
            views: [
                {
                    type: 'view',
                    css: {
                        left: '40rpx',
                        top: '144rpx',
                        background: '#fff',
                        radius: '16rpx',
                        width: '670rpx',
                        height: '930rpx',
                        shadow: '0 20rpx 48rpx rgba(0,0,0,.05)'
                    }
                },
                {
                    type: 'image',
                    src: '../../static/avatar-2.jpg',
                    mode: 'widthFix',
                    css: {
                        left: '40rpx',
                        top: '40rpx',
                        width: '84rpx',
                        height: '84rpx',
                        radius: '50%',
                        color: '#999'
                    }
                },
                {
                    type: 'text',
                    text: '隔壁老王',
                    css: {
                        color: '#333',
                        left: '144rpx',
                        top: '40rpx',
                        fontSize: '32rpx',
                        fontWeight: 'bold'
                    }
                },
                {
                    type: 'text',
                    text: '为您挑选了一个好物',
                    css: {
                        color: '#666',
                        left: '144rpx',
                        top: '90rpx',
                        fontSize: '24rpx'
                    }
                },
                {
                    type: 'image',
                    src: '../../static/goods.jpg',
                    mode: 'widthFix',
                    css: {
                        left: '72rpx',
                        top: '176rpx',
                        width: '606rpx',
                        height: '606rpx',
                        radius: '12rpx'
                    }
                },
                {
                    type: 'text',
                    text: '￥39.90',
                    css: {
                        color: '#FF0000',
                        left: '66rpx',
                        top: '812rpx',
                        fontSize: '56rpx',
                        fontWeight: 'bold'
                    }
                },
                {
                    type: 'text',
                    text: '360儿童电话手表9X 智能语音问答定位支付手表 4G全网通20米游泳级防水视频通话拍照手表男女孩星空蓝',
                    css: {
                        maxLines: 2,
                        width: '396rpx',
                        color: '#333',
                        left: '72rpx',
                        top: '948rpx',
                        fontSize: '36rpx',
                        lineHeight: '50rpx'
                    }
                },
                {
                    type: 'image',
                    src: '../../static/qr.png',
                    mode: 'widthFix',
                    css: {
                        left: '500rpx',
                        top: '864rpx',
                        width: '178rpx',
                        height: '178rpx'
                    }
                }
            ]
		}
    }
},
methods: {
    saveImage() {
        this.isShowPopup = false
        uni.saveImageToPhotosAlbum({
            filePath: this.path,
            success(res) {
                uni.showToast({
                    title: '已保存到相册',
                    icon: 'success',
                    duration: 2000
                })
            }
        })
    },
}
}

```

### API

#### Props

| 参数          | 说明         | 类型             | 默认值       |
| ------------- | ------------ | ---------------- | ------------ |
| board         | 画板对象     | <em>object</em>  | 参数请向下看 |
| customStyle   | 自定义样式   | <em>string</em>  |              |
| isRenderImage | 是否生成图片，在`@success`事件接收图片地址 | <em>boolean</em> | `false`      |
| isH5PathToBase64 | H5端把网络图片转成base64,解决部分跨域问题 | <em>boolean</em> | `false`      |
| isBase64ToPath | H5端把base64转成网络图片,应用场景不多考虑删除慎用 | <em>boolean</em> | `false`      |
| sleep | 此参数是为解决图片渲染时不按JSON层次前后渲染。若有此情况请增大数值 | <em>number</em> | `33`  |
| type | 只对微信小程序可有效 | <em>string</em> | `2d`  |
| fileType | 生成图片的后缀类型 | <em>string</em> | `png`  |
| pixelRatio | 生成图片的像素密度，默认为对应手机的像素密度 | <em>number</em> | ``  |
| width | 画板的宽度，一般只用于通过内部方法时加上 | <em>number</em> | ``  |
| height | 画板的高度  ，同上 | <em>number</em> | ``  |

<br>

#### Board

| 参数       | 说明                               | 类型            |
| ---------- | ---------------------------------- | --------------- |
| width      | 画板的宽度                         | <em>string</em> |
| height     | 画板的高度                         | <em>string</em> |
| background | 画板的背景色,支持颜色渐变，但要写百分比及方向如：`background: 'linear-gradient(to right, #ff971b 0%, #ff5000 100%)'`                      | <em>string</em> |
| views      | 画板的元素集，请向下看各元素的参数 | <em>object</em> |

<br>

#### 块 View

| 参数 | 说明                                                                                                |
| ---- | --------------------------------------------------------------------------------------------------- |
| type | 元素类型`view`                                                                                      |
| css  | 元素的样式，`top`、`left`、`width`、`height`、`background`、`radius`、`border`、`shadow` 、`transform`包含缩放: `scaleX(-1)`、旋转`rotate(50deg)`、倾斜`skewX(50deg)` |

<br>

#### 文本 text

| 参数 | 说明                                                                                                                                                                        |
| ---- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| type | 元素类型`text`                                                                                                                                                              |
| text | 文本内容                                                                                                                                                                    |
| css  | 元素的样式，`top`、`left`、`fontSize`、`fontWeight`、`fontFamily`、`width`、`lineHeight`、`color`、`textDecoration`、`textAlign`：center, left, right、最大行数：`maxLines`、`transform` |
| rules | 是一个对象 `Object`指定文字颜色, `word`关键词数组、`color`关键词颜色，目前只能定一种颜色。|
				
<br>

#### 图片 image

| 参数 | 说明                                                                       |
| ---- | -------------------------------------------------------------------------- |
| type | 元素类型`image`                                                            |
| src  | 图片地址                                                                   |
| css  | 元素的样式，`top`、`left`、`width`、`height`、`radius`、`border`、`shadow`、`transform` |

<br>

#### 二维码 qrcode

| 参数 | 说明                                                                       |
| ---- | -------------------------------------------------------------------------- |
| type | 元素类型`qrcode`, 二维码。[需要自行下载覆盖](https://gitee.com/liangei/lime-painter/blob/master/qrcode.js)。                                                            |
| text  | 二维码文本内容                                                                   |
| css  | 元素的样式，`top`、`left`、`width`、`height`、`border`、`transform`、`backgroundColor`背景色、`color` 前景色|

<br>

#### 事件 Events

| 事件名  | 说明         | 回调           |
| ------- | ------------ | -------------- |
| success | 生成图片成功,若使用了`isRenderImage`可以接收图片地址 |	event      |
| fail    | 生成图片失败 | {error: error} |
| done    | 绘制成功 |  |

### 常见问题
> 1、H5端使用网络图片需要解决跨域问题、或者添加`isH5PathToBase64`可解决部分问题。<br>
> 2、小程序使用网络图片需要去公众平台增加下载白名单！二级域名也需要配！<br>
> 3、H5端生成图片是base64，有时显示只有一半可以使用原生标签`<IMG/>`<br>
> 4、发生保存图片倾斜变形或提示native buffer exceed size limit时，使用pixel-ratio="2"参数，降分辨率。<br>
> 5、h5保存图片不需要调接口，提示用户长按图片保存。<br>
> 6、IOS APP 请勿使用HBX2.9.3.20201014的版本！这个版本无法生成图片。<br>
> 7、不生成图片或不绘制时，请查看是否把组件隐藏了！<br>
> 8、APP端无成功反馈、也无失败反馈时，请更新基座和HBX。<br>
> 9、微信小程序2D不支持真机调试，请使用真机预览方式。<br>
>10、华为手机APP上无法生成图片，请使用HBX2.9.11++<br>
>11、苹果微信7.0.20存在闪退和图片无法onload为微信bug,请到码云上升级本插件<br>
### 打赏
如果你觉得本插件，解决了你的问题，赠人玫瑰，手留余香。<br>
![输入图片说明](https://images.gitee.com/uploads/images/2020/1122/222521_bb543f96_518581.jpeg "微信图片编辑_20201122220352.jpg")