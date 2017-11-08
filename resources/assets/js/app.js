require('./bootstrap');

import store from './store/index';
import router from './router';
import $ from 'jquery';
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue');

//Components
Vue.component('youtube-player-card', require('./components/YouTubePlayerCard.vue'));
//Forms
Vue.component('import-form', require('./forms/Import.vue'));

//
router.beforeEach((to, from, next) => {

  if(to.path !== "/")
    $('#hardContent').remove();

  next();
});

const app = new Vue({
  router,
  store
}).$mount('#app');
