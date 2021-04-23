import Vue from 'vue'
import DiscoverService from '../../services/api/DiscoverService'

export const namespaced = true
export const state = {
  similarTracks: {},
  errorMessage: false,
}

export const mutations = {
  SET_ERROR_MESSAGE(state, message) {
    state.errorMessage = message
  },
  SET_SIMILAR_TRACKS(state, { videoId, items }) {
    Vue.set(state.similarTracks, videoId, items)
  },
}

export const getters = {
  similarTracks(state) {
    return videoId => {
      return state.similarTracks[videoId]
    }
  },
}

export const actions = {
  getSimilarTracks(context, videoId) {
    DiscoverService.getSimilarTracksByVideoId(videoId)
      .then(response => {
        // Clear error message
        context.commit('SET_ERROR_MESSAGE', false)
        if (response.data?.items) {
          context.commit('SET_SIMILAR_TRACKS', {
            videoId,
            items: response.data.items,
          })
        }
      })
      .catch(response => {
        if (response.status == 500) {
          context.commit('SET_ERROR_MESSAGE', response.data.message)
          return
        }
      })
  },
}
