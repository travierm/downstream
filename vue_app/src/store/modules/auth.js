import router from '@/router/index';
import { fetchInitUserData } from '../events';
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
    SET_USER(state, user) {
        state.user = user
    },
    CLEAR_USER() {
        window.localStorage.clear()
    }
}

export const getters = {
    loggedIn: state => {
        return !!state.user;
    }
}

export const actions = {
    login({ commit, dispatch }, params) {
        commit("SET_LOADING", true);

        return AuthService.login(params)
            .then(response => { 
                commit("SET_TOKEN", response.data.token);
                commit("SET_LOADING", false);
                commit("SET_ERROR", false)

                // Fetch init user data
                fetchInitUserData()
                
            }).catch(error => {
                commit("SET_ERROR", error)
            })
    },
    logout({ commit, getters }) {
        if(!getters.loggedIn) {
            return true
        }

        commit("SET_USER", false)

        return AuthService.logout()
            .then(() => {
                commit("CLEAR_USER")
            })
            .catch(() => {
                commit("CLEAR_USER")
            });
    },
    getUser({ commit, getters}) {
        if(getters.loggedIn) {
            return
        }

        return AuthService.getUser()
            .then((response) => {
                if(response.data) {
                    commit("SET_USER", response.data.user)
                }
            }).catch(error => {
                router.push('/login')
            })
    }
}