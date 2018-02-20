import $ from 'jquery';
import { sync } from 'vuex-router-sync';
import store from './store/index';
import router from './router';
import BootstrapVue from 'bootstrap-vue';

require('./bootstrap');


const unsync = sync(store, router);
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue');

Vue.use(BootstrapVue);

// Components
Vue.component('youtube-player-card', require('./components/YouTubePlayerCard.vue'));
Vue.component('video-player-card', require('./components/VideoPlayerCard.vue'));
Vue.component('master-bar', require('./components/MasterBar.vue'));
// Forms
Vue.component('import-form', require('./forms/Import.vue'));

router.beforeEach((to, from, next) => {
  if (to.path !== '/') { $('#hardContent').remove(); }

  store.dispatch('video/unregisterAll');

  next();
});

// Make initial api requests
store.dispatch('collection/update');
store.dispatch('video/unregisterAll');

function fixVideos() {
  // Find all YouTube videos
var $allVideos = $("iframe[src^='//www.youtube.com']"),

// The element that is fluid width
$fluidEl = $("body");

// Figure out and save aspect ratio for each video
$allVideos.each(function() {

  $(this)
    .data('aspectRatio', this.height / this.width)

    // and remove the hard coded width/height
    .removeAttr('height')
    .removeAttr('width');
  });

  // When the window is resized
  $(window).resize(function() {

    var newWidth = $fluidEl.width();

    // Resize all videos according to their own aspect ratio
    $allVideos.each(function() {

      var $el = $(this);
      $el
        .width(newWidth)
        .height(newWidth * $el.data('aspectRatio'));

  });

  // Kick off one resize to fix all videos on page load
  }).resize();
}

fixVideos();

const app = new Vue({
  router,
  store,
}).$mount('#app');
