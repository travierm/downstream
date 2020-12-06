import $ from 'jquery';
import Vue from "vue";
import { sync } from 'vuex-router-sync';
import store from './store/index';
import router from './router';
import BootstrapVue from 'bootstrap-vue';
import VueScrollTo from 'vue-scrollto';

// Components
import About from './pages/about';
import ControlBar from './components/ControlBar'
import ThemeButton from './components/ThemeButton'
import RawDataView from './components/RawDataView'
import FollowButton from './components/FollowButton'
import YouTubeCard from './components/YouTubeCard/Card'
import SpotifyAuthorizeButton from './components/SpotifyAuthorizeButton'

// Forms
import ImportForm from './forms/Import';

window.Vue = Vue

require('./bootstrap');

sync(store, router);

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
Vue.component('about-page', About);
Vue.component('theme-btn', ThemeButton);
Vue.component('control-bar', ControlBar);
Vue.component('import-form', ImportForm);
Vue.component('youtube-card', YouTubeCard);
Vue.component('raw-data-view', RawDataView);
Vue.component('follow-button', FollowButton);
Vue.component('spotify-authorize-btn', SpotifyAuthorizeButton);

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

const app = new Vue({
  router,
  store
}).$mount('#app');