import store from '../store/index';

export function applyMiddleware(router) {
    // Search Middleware
    router.beforeEach((to, from, next) => {

        if(to.path == '/search' && to.query.query) {
            store.dispatch('search/setQuery', to.query.query)
        }

        next();
    })
}