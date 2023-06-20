import { BrowserTracing } from '@sentry/tracing';
import * as Sentry from '@sentry/vue';
import Vue from 'vue';
import { sync } from 'vuex-router-sync';

import App from './App.vue';
import loadGlobalComponents from './global_components';
import vuetify from './plugins/vuetify';
import router from './router';
import store from './store';

Vue.config.productionTip = false

const unsync = sync(store, router)

Sentry.init({
  Vue,
  dsn: 'https://f24b1b8189b74b0c89c082f81f99fddd@o1142461.ingest.sentry.io/6201485',
  integrations: [
    new BrowserTracing({
      routingInstrumentation: Sentry.vueRouterInstrumentation(router),
      tracingOrigins: ['api.downstream.us', /^\//],
    }),
  ],
  // Set tracesSampleRate to 1.0 to capture 100%
  // of transactions for performance monitoring.
  // We recommend adjusting this value in production
  tracesSampleRate: 1.0,
  enabled: process.env.NODE_ENV !== 'development',
})

const loggedIn = store.getters['auth/loggedIn']
if (!loggedIn && store.state.auth.token) {
  store.dispatch('auth/getUser')
}

if (process.env.NODE_ENV == 'development') {
  window.dd = console.log
} else {
  window.dd = () => {}
}

loadGlobalComponents(Vue)

new Vue({
  router,
  vuetify,
  store,
  render: (h) => h(App),
}).$mount('#app')
