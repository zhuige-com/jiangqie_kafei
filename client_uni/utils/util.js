/*
 * 酱茄小程序开源版 v1.5.0
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/ky
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * Copyright © 2020-2021 www.jiangqie.com All rights reserved.
 */

function navigateBack() {
	uni.navigateBack({
		delta: 1,
		fail: function(res) {
			uni.switchTab({
				url: '/pages/index/index'
			});
		}
	});
}

module.exports = {
	navigateBack
};
