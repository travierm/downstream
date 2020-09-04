import $ from 'jquery';
import { sync } from 'vuex-router-sync';
import store from './store/index';
import router from './router';
import BootstrapVue from 'bootstrap-vue';
import VueScrollTo from 'vue-scrollto';
import consl from './services/Consl';

window.Vue = require('vue');
window.consl = consl;

require('./bootstrap');

const unsync = sync(store, router);

Vue.use(BootstrapVue);

Vue.use(VueScrollTo, {
  container: "body",
    duration: 500,
    easing: "ease",
    offset: -200,
    cancelable: true,
    onStart: false,
    onDone: false,
    onCancel: false,
    x: false,
    y: true
});

// Components
Vue.component('follow-button', require("./components/FollowButton.vue"));
Vue.component('theme-btn', require("./components/ThemeButton.vue"));
Vue.component('spotify-authorize-btn', require("./components/SpotifyAuthorizeButton.vue"));
Vue.component('playlist-media-manager', require('./components/PlaylistMediaManager.vue'));
//Vue.component('video-player-card', require('./components/VideoPlayerCard.vue'));
Vue.component('youtube-card', require('./components/YouTubeCard.vue'));
//Vue.component('master-bar', require('./components/MasterBar.vue'));
Vue.component('control-bar', require('./components/ControlBar.vue'));
Vue.component('raw-data-view', require('./components/RawDataView.vue'));
// Forms
Vue.component('import-form', require('./forms/Import.vue'));

//Pages
Vue.component('about-page', require('./pages/about.vue'));

router.beforeEach((to, from, next) => {
  $('.navbar-collapse').collapse('hide');
	//do something before routing
  //fires on pageload
  store.dispatch('player/reset');

  if(to.path == "/search") {
    window.onload = function() {

      //logic for moving cursor after search submit
      $('#searchInputBar').focus(function(){
        var that = this;
        setTimeout(function(){ that.selectionStart = that.selectionEnd = 10000; }, 0);
      });
      
      document.getElementById("searchInputBar").focus();
    };
  }

	//Hide PHP generated html when route name not set
	if (to.name) { 
		$('#hardContent').remove(); 
	}

  	next();
});

//get user info
store.dispatch('user/getUserInfo');
store.dispatch('collection/update');

//fix bootstrap mobilenavbar issue
$('.nav-link').on('click', function(){
  console.log("CLICK CALLED!");
  
});

const app = new Vue({
  router,
  store
}).$mount('#app');