import Vue from 'vue'
import App from './App.vue'
import router from './router'
import vuetify from './plugins/vuetify';
import store from './store'

Vue.config.productionTip = false

const loggedIn = store.getters["auth/loggedIn"]
if (!loggedIn && store.state.auth.token) {
    store.dispatch("auth/getUser")
}

if(process.env.NODE_ENV == 'development') {
  window.dd = console.log
}else {
  window.dd = () => {}
}

new Vue({
  router,
  vuetify,
  store,
  render: h => h(App)
}).$mount('#app')
