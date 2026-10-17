import * as Sentry from '@sentry/vue'
import { createApp } from 'vue'

import App from './App.vue'
import loadGlobalComponents from './global_components'
import vuetify from './plugins/vuetify'
import router from './router'
import store from './store'

const app = createApp(App)
app.use(store).use(router).use(vuetify)
loadGlobalComponents(app)

Sentry.init({
  app,
  dsn: 'https://f24b1b8189b74b0c89c082f81f99fddd@o1142461.ingest.sentry.io/6201485',
  integrations: [],
  tracesSampleRate: 1.0,
  enabled: import.meta.env.PROD,
})

window.dd = import.meta.env.DEV ? console.log : () => {}

store.watch(
  () => store.getters['auth/loggedIn'],
  loggedIn => {
    if (!loggedIn && router.currentRoute.value.meta.requiresAuth) router.replace('/login')
  },
)

if (!store.getters['auth/loggedIn'] && store.state.auth.token) {
  store.dispatch('auth/getUser')
}

router.isReady().then(() => app.mount('#app'))
