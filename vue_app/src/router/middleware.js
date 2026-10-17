import store from '../store/index'

export function applyMiddleware(router) {
  // Search Middleware
  router.beforeEach((to, from) => {
    if (to.path == '/search' && to.query.query) {
      store.dispatch('search/setQuery', to.query.query)
    }

    if (from.fullPath.includes('playlist_id') && to.path == '/collection') {
      store.dispatch('playlist/setSelectedPlaylist', false)
      store.dispatch('collection/fetchCollection')
    }
  })

  // Redirect to landing page when not loggedIn on requiresAuth routes
  router.beforeEach((to) => {
    const loggedIn = store.getters['auth/loggedIn']

    if (to.matched.some((record) => record.meta.requiresAuth)) {
      if (loggedIn == false) {
        return '/'
      }
    }

    if (to.matched.some((record) => record.meta.requiresAdmin) && store.state.auth.user?.type !== 'admin') {
      return loggedIn ? '/collection' : '/'
    }
  })

  // If path /login and already loggedIn redirect to /collection
  router.beforeEach((to) => {
    const loggedIn = store.getters['auth/loggedIn']

    if (to.path == '/login' && loggedIn) {
      return '/collection'
    }

    if (to.path == '/' && loggedIn) {
      return '/collection'
    }
  })
}
