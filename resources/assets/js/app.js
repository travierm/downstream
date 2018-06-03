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
    offset: -50,
    cancelable: true,
    onStart: false,
    onDone: false,
    onCancel: false,
    x: false,
    y: true
});

// Components
Vue.component('video-player-card', require('./components/VideoPlayerCard.vue'));
Vue.component('master-bar', require('./components/MasterBar.vue'));
Vue.component('control-bar', require('./components/ControlBar.vue'));
// Forms
Vue.component('import-form', require('./forms/Import.vue'));

//Pages
Vue.component('about-page', require('./pages/about.vue'));

router.beforeEach((to, from, next) => {
	//do something before routing
	//fires on pageload
	
	//clear player data
	store.dispatch('media/resetState');

	//Hide PHP generated html when route name not set
	if (to.name) { 
		$('#hardContent').remove(); 
	}

  	next();
});

const app = new Vue({
  router,
  store
}).$mount('#app');