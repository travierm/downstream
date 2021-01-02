import store from "../store/index"

export function applyMiddleware(router) {
    // Search Middleware
    router.beforeEach((to, from, next) => {
        if (to.path == "/search" && to.query.query) {
            store.dispatch("search/setQuery", to.query.query)
        }

        next()
    })

    // Redirect to landing page when not loggedIn on requiresAuth routes
    router.beforeEach(async(to, from, next) => {
        const loggedIn = store.getters["auth/loggedIn"]

        if (to.matched.some((record) => record.meta.requiresAuth)) {
            if (loggedIn == false) {
                return next("/")
            }
        }

        return next()
    })

    // If path /login and already loggedIn redirect to /collection
    router.beforeEach((to, from, next) => {
        const loggedIn = store.getters["auth/loggedIn"]

        if (to.path == "/login" && loggedIn) {
            return next("/collection")
        }

        return next()
    })
}
