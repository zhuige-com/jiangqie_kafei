/*
 * 酱茄小程序开源版 v1.2.2
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/ky
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * License：MIT
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