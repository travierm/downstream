import YoutubePlayerManager from "../../services/YoutubePlayerManager"

export const namespaced = true
export const state = {
    guidIndex: []
}
export const getters = {}

export const mutations = {
    SET_GUID_INDEX(state, index) {
        state.guidIndex = index
    }
}

export const actions = {
    setGuidIndex({ commit }, index) {
        commit('SET_GUID_INDEX', index)
        YoutubePlayerManager.setGuidIndex(index)
    },
}
