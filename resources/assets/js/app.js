import $ from 'jquery';
import { sync } from 'vuex-router-sync';
import store from './store/index';
import router from './router';
import BootstrapVue from 'bootstrap-vue';
import VueAnalytics from 'vue-analytics';
import consl from './services/Consl';

window.consl = consl;

require('./bootstrap');

const unsync = sync(store, router);
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue');
var VueScrollTo = require('vue-scrollto');

Vue.use(BootstrapVue);
Vue.use(VueScrollTo);

VueScrollTo.setDefaults({
    container: "body",
    duration: 500,
    easing: "ease",
    offset: -50,
    cancelable: true,
    onStart: false,
    onDone: false,
    onCancel: false,
    x: false,
    y: true
})

// Components
Vue.component('youtube-player-card', require('./components/YouTubePlayerCard.vue'));
Vue.component('video-player-card', require('./components/VideoPlayerCard.vue'));
Vue.component('master-bar', require('./components/MasterBar.vue'));
// Forms
Vue.component('import-form', require('./forms/Import.vue'));

//Pages
Vue.component('about-page', require('./pages/about.vue'));

const hardPaths = [
	'/',
	'/all',
	'/hash',
	'/login',
	'/logout',
	'/register',
	'/media',
	'/admin/dash'
];

router.beforeEach((to, from, next) => {
	//Hide PHP generated html when not on a hard path
	if (!isHardPath(to.path)) { $('#hardContent').remove(); }

  	next();
});

// Make initial api requests
store.dispatch('media/getCollection');

Vue.use(VueAnalytics, {
  id: "UA-111656856-1",
  router
});

const app = new Vue({
  router,
  store
}).$mount('#app');

//
function isHardPath(path) {
	for(var i = 0; i <= path.length; i++) {
		let hardPath = hardPaths[i];

		if(hardPath == '/media' && path.includes(hardPath)) {
			return true;
		}

		if(path == hardPath) {
			return true;
		}
	}

	return false;
}
