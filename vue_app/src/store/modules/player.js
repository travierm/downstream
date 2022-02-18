import _ from 'lodash'
import YoutubePlayerManager from '../../services/YoutubePlayerManager'

export const namespaced = true
export const state = {
  guidIndex: [],
}
export const getters = {}

export const mutations = {
  SET_GUID_INDEX(state, index) {
    state.guidIndex = index
  },
}

export const actions = {
  playGuid({}, guid) {
    YoutubePlayerManager.playGuid(guid)
  },
  setGuidIndex({ commit }, index) {
    commit('SET_GUID_INDEX', index)

    YoutubePlayerManager.setGuidIndex(index)
  },
  updateGuidVideoMap({}, map) {
    YoutubePlayerManager.updateGuidVideoMap(map)
  },
}
