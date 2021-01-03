import CollectionService from "@/services/api/CollectionService"

export const namespaced = true
export const state = {
    searchQuery: "",
    hash: window.localStorage.getItem("collectionHash"),
    collection: JSON.parse(window.localStorage.getItem("collection")),
}

export const mutations = {
    SET_SEARCH_QUERY(state, query) {
        state.searchQuery = query
    },
    UPDATE_COLLECTION(state, data) {
        const { hash, items } = data

        state.hash = hash
        state.collection = Object.freeze(items)

        window.localStorage.setItem("collectionHash", hash)
        window.localStorage.setItem("collection", JSON.stringify(items))
    },
}

export const getters = {
    collectionSearchResults(state) {
        const searchQuery = state.searchQuery

        if(!searchQuery) {
            return state.collection
        }

        return state.collection.filter((video) => {
            return video.title.toLowerCase().includes(searchQuery.toLowerCase())
        })
    },
}

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

        const response = await CollectionService.fetchCollection()
        if (response.data) {
            if (response.data.hash !== state.hash) {
                console.log(
                    "Collection hash does not match. Updating local state!"
                )

                commit("UPDATE_COLLECTION", response.data)
            }
        }
    },
}
