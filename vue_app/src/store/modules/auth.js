import AuthService from '@/services/api/AuthService';

export const namespaced = true;
export const state = {
    user: false,
    error: false,
    loading: false,
    token: window.localStorage.getItem("token"),
}

export const mutations = {
    SET_TOKEN(state, token) {
        state.token = token;
        window.localStorage.setItem("token", token);
    },
    SET_LOADING(state, boolean) {
        state.loading = boolean
    },
    SET_ERROR(state, errorText) {
        state.error = errorText
    },
    CLEAR_USER() {
        window.localStorage.clear()
        location.reload()
    }
}

export const getters = {

}

export const actions = {
    login({ commit }, params) {
        commit("SET_LOADING", true);

        return AuthService.login(params)
            .then(response => { 
                commit("SET_TOKEN", response.data.token);
                commit("SET_LOADING", false);
            }).catch(error => {
                commit("SET_ERROR", error)
                console.error(error)
            })
    },
    logout() {
        return AuthService.logout()
            .then(() => {
                commit("CLEAR_USER");
            })
            .catch(() => {
                commit("CLEAR_USER");
            });
    }
}