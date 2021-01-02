import store from "../store/index"

export function applyMiddleware(router) {
    // const loggedIn = store.getters["auth/loggedIn"]
    
    // Search Middleware
    router.beforeEach((to, from, next) => {
        if (to.path == "/search" && to.query.query) {
            store.dispatch("search/setQuery", to.query.query)
        }

        next()
    })

    // Redirect to landing page when not loggedIn on requiresAuth routes
    router.beforeEach((to, from, next) => {
        const loggedIn = store.getters["auth/loggedIn"]

        if (to.path == "/login" && loggedIn) {
            next('/collection')
        }

        next()
    })
}
