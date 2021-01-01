import CollectionService from "@/services/api/CollectionService"

export const namespaced = true
export const state = {
    userCollection: false,
}

export const mutations = {
    SET_USER_COLLECTION(state, items) {
        state.userCollection = Object.freeze(items)
    },
}

export const getters = {}

export const actions = {
    collectItem(commit, videoId) {
        return CollectionService.collectItem(videoId)
    },
    removeItem(commit, itemId) {
        return CollectionService.removeItem(itemId)
    },
    async fetchUserCollection({ commit, rootState, state }) {
        if (!rootState.auth.token) {
            return
        }

        let response = await CollectionService.fetchUserCollection()

        if (response.data) {
            commit("SET_USER_COLLECTION", response.data.items)
        }
    },
}
