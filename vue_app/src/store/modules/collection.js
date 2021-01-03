import CollectionService from "@/services/api/CollectionService"

export const namespaced = true
export const state = {
    hash: window.localStorage.getItem("collectionHash"),
    collection: JSON.parse(window.localStorage.getItem("collection")),
}

export const mutations = {
    UPDATE_COLLECTION(state, data) {
        const { hash, items } = data

        state.hash = hash
        state.collection = Object.freeze(items)

        window.localStorage.setItem("collectionHash", hash)
        window.localStorage.setItem("collection", JSON.stringify(items))
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
    async fetchCollection({ commit, rootState, state }) {
        if (!rootState.auth.token) {
            return
        }

        let response = await CollectionService.fetchCollection()
        if (response.data) {
            if(response.data.hash !== state.hash) {
                console.log('Collection hash does not match. Updating local state!')

                commit("UPDATE_COLLECTION", response.data)
            }
        }
    },
}
