import $ from 'jquery';
import { sync } from 'vuex-router-sync';
import store from './store/index';
import router from './router';

require('./bootstrap');


const unsync = sync(store, router);
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue');

// Components
Vue.component('youtube-player-card', require('./components/YouTubePlayerCard.vue'));
Vue.component('master-bar', require('./components/MasterBar.vue'));
// Forms
Vue.component('import-form', require('./forms/Import.vue'));

router.beforeEach((to, from, next) => {
  if (to.path !== '/') { $('#hardContent').remove(); }

  next();
});

// Make initial api requests
store.dispatch('collection/update');

const app = new Vue({
  router,
  store,
}).$mount('#app');
