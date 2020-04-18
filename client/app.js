/*
 * 酱茄小程序开源版 v1.1.0
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * License：MIT
 * Copyright ️ 2020 www.jiangqie.com All rights reserved.
 */

const Auth = require('./utils/auth.js');

App({

    app_name: '酱茄',

    onLaunch: function () {
        Auth.checkSession();
    },
    
})