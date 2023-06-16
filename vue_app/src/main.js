import { createApp } from 'vue';
import { sync } from 'vuex-router-sync';

import App from './App.vue';
import loadGlobalComponents from './global_components';
import vuetify from './plugins/vuetify';
import router from './router';
import store from './store';

//Vue.config.productionTip = false

const unsync = sync(store, router)

const loggedIn = store.getters['auth/loggedIn']
if (!loggedIn && store.state.auth.token) {
  store.dispatch('auth/getUser')
}

if (process.env.NODE_ENV == 'development') {
  window.dd = console.log
} else {
  window.dd = () => {}
}



const vue = createApp(App).use(router).use(vuetify).use(store).mount('#app')

loadGlobalComponents(vue)
