import Vue from "vue"
import Vuex from "vuex"

import * as auth from "./modules/auth"
import * as search from "./modules/search"
import * as collection from "./modules/collection"

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        showLoadingBar: false
    },
    mutations: {
        SET_LOADING_BAR_STATE(state, boolean) {
            state.showLoadingBar = boolean
        }
    },
    actions: {
        setLoadingBarState(context, boolean) {
            context.commit('SET_LOADING_BAR_STATE', boolean)
        }
    },
    modules: {
        auth,
        search,
        collection,
    },
})
