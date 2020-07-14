import auth from '@/services/api/auth';

export default {
    namespaced: true,
    state: {
        loggedIn: false
    },
    actions: {
        initCSRF() {
            auth.initCSRF()
        }
    },
    mutations: {

    }
};