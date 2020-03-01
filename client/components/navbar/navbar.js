Component({
    options: {
        multipleSlots: true,
        addGlobalClass: true
    },
    properties: {
        extClass: {
            type: String,
            value: ''
        },
        background: {
            type: String,
            observer: '_showChange'
        },
        backgroundColorTop: {
            type: String,
            // value: 'rgba(224, 48, 31, 100)',
            observer: '_showChangeBackgroundColorTop'
        },
        color: {
            type: String,
            value: 'rgba(255, 255, 255, 1)'
        },
        title: {
            type: String,
            value: ''
        },
        searchText: {
            type: String,
            value: '点我搜索'
        },
        searchBar: {
            type: Boolean,
            value: false
        },
        back: {
            type: Boolean,
            value: false
        },
        home: {
            type: Boolean,
            value: false
        },
        iconTheme: {
            type: String,
            value: 'black'
        },
        animated: {
            type: Boolean,
            value: true
        },
        show: {
            type: Boolean,
            value: true,
            observer: '_showChange'
        },
        delta: {
            type: Number,
            value: 1
        }
    },
    created() {
        this.getSystemInfo()
    },
    attached() {
        this.setStyle() // 设置样式
    },
    data: {},
    pageLifetimes: {
        show() {
            if (getApp().globalSystemInfo.ios) {
                this.getSystemInfo()
                this.setStyle() // 设置样式1
            }
        },
        hide() {}
    },
    methods: {
        setStyle(life) {
            const {
                statusBarHeight,
                navBarHeight,
                capsulePosition,
                navBarExtendHeight,
                ios,
                windowWidth
            } = getApp().globalSystemInfo
            const {
                back,
                home,
                title
            } = this.data
            const rightDistance = windowWidth - capsulePosition.right // 胶囊按钮右侧到屏幕右侧的边距
            const leftWidth = windowWidth - capsulePosition.left // 胶囊按钮左侧到屏幕右侧的边距

            const navigationbarinnerStyle = [
                `color: ${this.data.color}`,
                `background: ${this.data.background}`,
                `height:${navBarHeight + navBarExtendHeight}px`,
                `padding-top:${statusBarHeight}px`,
                `padding-right:${leftWidth}px`,
                `padding-bottom:${navBarExtendHeight}px`
            ].join(';')
            let navBarLeft = []
            if ((back && !home) || (!back && home)) {
                navBarLeft = [`width:${capsulePosition.width}px`, `height:${capsulePosition.height}px`].join(';')
            } else if ((back && home) || title) {
                navBarLeft = [
                    `width:${capsulePosition.width}px`,
                    `height:${capsulePosition.height}px`,
                    `margin-left:${rightDistance}px`
                ].join(';')
            } else {
                navBarLeft = ['width:auto', 'margin-left:0px'].join(';')
            }
            if (life === 'created') {
                this.data = {
                    navigationbarinnerStyle,
                    navBarLeft,
                    navBarHeight,
                    capsulePosition,
                    navBarExtendHeight,
                    ios
                }
            } else {
                this.setData({
                    navigationbarinnerStyle,
                    navBarLeft,
                    navBarHeight,
                    capsulePosition,
                    navBarExtendHeight,
                    ios
                })
            }
        },
        _showChange() {
            this.setStyle()
        },
        // 返回事件
        back() {
            this.triggerEvent('back', {
                delta: this.data.delta
            })
        },
        home() {
            this.triggerEvent('home', {})
        },
        search() {
            this.triggerEvent('search', {})
        },
        checkRect(rect) { // 胶囊信息4种任一属性为0返回true
            return !rect.width || !rect.top || !rect.left || !rect.height
        },
        getMenuButtonBoundingClientRect(systemInfo) {
            const ios = !!(systemInfo.system.toLowerCase().search('ios') + 1)
            let rect
            try {
                rect = wx.getMenuButtonBoundingClientRect ? wx.getMenuButtonBoundingClientRect() : null
                if (rect === null) {
                    throw new Error('getMenuButtonBoundingClientRect error')
                }
                // 取值为0的情况  有可能width不为0 top为0的情况
                if (this.checkRect(rect)) {
                    throw new Error('getMenuButtonBoundingClientRect error')
                }
            } catch (error) {
                let gap = '' // 胶囊按钮上下间距 使导航内容居中
                let width = 96 // 胶囊的宽度
                if (systemInfo.platform === 'android') {
                    gap = 8
                    width = 96
                } else if (systemInfo.platform === 'devtools') {
                    if (ios) {
                        gap = 5.5 // 开发工具中ios手机
                    } else {
                        gap = 7.5 // 开发工具中android和其他手机
                    }
                } else {
                    gap = 4
                    width = 88
                }
                if (!systemInfo.statusBarHeight) {
                    // 开启wifi的情况下修复statusBarHeight值获取不到
                    systemInfo.statusBarHeight = systemInfo.screenHeight - systemInfo.windowHeight - 20
                }
                rect = {
                    // 获取不到胶囊信息就自定义重置一个
                    bottom: systemInfo.statusBarHeight + gap + 32,
                    height: 32,
                    left: systemInfo.windowWidth - width - 10,
                    right: systemInfo.windowWidth - 10,
                    top: systemInfo.statusBarHeight + gap,
                    width
                }
            }
            return rect
        },
        getSystemInfo() {
            const app = getApp()
            if (app.globalSystemInfo && !app.globalSystemInfo.ios) {
                return app.globalSystemInfo
            } else {
                const systemInfo = wx.getSystemInfoSync()
                const ios = !!(systemInfo.system.toLowerCase().search('ios') + 1)
                const rect = this.getMenuButtonBoundingClientRect(systemInfo)

                let navBarHeight = ''
                if (!systemInfo.statusBarHeight) {
                    systemInfo.statusBarHeight = systemInfo.screenHeight - systemInfo.windowHeight - 20
                    navBarHeight = (function () {
                        const gap = rect.top - systemInfo.statusBarHeight
                        return 2 * gap + rect.height
                    }())

                    systemInfo.statusBarHeight = 0
                    systemInfo.navBarExtendHeight = 0 // 下方扩展4像素高度 防止下方边距太小
                } else {
                    navBarHeight = (function () {
                        const gap = rect.top - systemInfo.statusBarHeight
                        return systemInfo.statusBarHeight + 2 * gap + rect.height
                    }())
                    if (ios) {
                        systemInfo.navBarExtendHeight = 4 // 下方扩展4像素高度 防止下方边距太小
                    } else {
                        systemInfo.navBarExtendHeight = 0
                    }
                }
                systemInfo.navBarHeight = navBarHeight // 导航栏高度不包括statusBarHeight
                systemInfo.capsulePosition = rect
                systemInfo.ios = ios // 是否ios

                app.globalSystemInfo = systemInfo // 将信息保存到全局变量中,后边再用就不用重新异步获取了

                // console.log('systemInfo', systemInfo);
                return systemInfo
            }
        }
    }
})