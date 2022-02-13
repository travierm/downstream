import Vue from 'vue'
import * as Sentry from '@sentry/vue'
import { Integrations } from '@sentry/tracing'

import App from './App.vue'
import router from './router'
import vuetify from './plugins/vuetify'
import store from './store'

Vue.config.productionTip = false

Sentry.init({
  Vue,
  dsn:
    'https://f24b1b8189b74b0c89c082f81f99fddd@o1142461.ingest.sentry.io/6201485',
  integrations: [
    new BrowserTracing({
      routingInstrumentation: Sentry.vueRouterInstrumentation(router),
      tracingOrigins: ['localhost', 'my-site-url.com', /^\//],
    }),
  ],
  // Set tracesSampleRate to 1.0 to capture 100%
  // of transactions for performance monitoring.
  // We recommend adjusting this value in production
  tracesSampleRate: 1.0,
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

new Vue({
  router,
  vuetify,
  store,
  render: h => h(App),
}).$mount('#app')
