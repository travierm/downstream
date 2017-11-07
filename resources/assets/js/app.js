
import store from './store';
import router from './router';
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');


//Components
Vue.component('youtube-player-card', require('./components/YouTubePlayerCard.vue'));
//Forms
Vue.component('import-form', require('./forms/Import.vue'));

const app = new Vue({
  router
}).$mount('#app');
