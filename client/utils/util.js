/*
 * 酱茄小程序开源版 v1.5.0
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/ky
 * github: https://github.com/zhuige-com/jiangqie_kafei
 * gitee: https://gitee.com/zhuige_com/jiangqie_kafei
 * Copyright © 2020-2021 www.jiangqie.com All rights reserved.
 */

function navigateBack() {
    wx.navigateBack({
        delta: 1,
        fail: function (res) {
            wx.switchTab({
              url: '/pages/index/index',
            })
        }
    });
}

module.exports = {
    navigateBack,
}