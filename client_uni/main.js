import Vue from 'vue';
import App from './App';

// Api函数polyfill（目前为实验版本，如不需要，可删除！）';
// import Polyfill from './polyfill/polyfill';
// Polyfill.init();

// 全局mixins，用于实现setData等功能';
import Mixin from './polyfill/mixins';
Vue.mixin(Mixin);

Vue.config.productionTip = false;

App.mpType = 'app';

const app = new Vue({
    ...App
});
app.$mount();
