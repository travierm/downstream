import Vue from 'vue'
import Cache from '../../services/Cache'
import DiscoverService from '../../services/api/DiscoverService'

const discoverSimilarTrackStorage = new Cache(true)
discoverSimilarTrackStorage.setStoragePrefix('discover_video_')

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
    const cachedTracks = discoverSimilarTrackStorage.get(videoId)

    if (cachedTracks) {
      console.log(`Using cached similar tracks for video_id ${videoId}`)

      context.commit('SET_SIMILAR_TRACKS', {
        videoId,
        items: JSON.parse(cachedTracks),
      })

      return
    }

    dispatch('setLoadingBarState', true, { root: true })
    return DiscoverService.getSimilarTracksByVideoId(videoId)
      .then(response => {
        dispatch('setLoadingBarState', false, { root: true })

        // Clear error message
        context.commit('SET_ERROR_MESSAGE', false)
        if (response.data?.items) {
          discoverSimilarTrackStorage.set(
            videoId,
            JSON.stringify(response.data.items)
          )
          context.commit('SET_SIMILAR_TRACKS', {
            videoId,
            items: response.data.items,
          })
        }
      })
      .catch(response => {
        dispatch('setLoadingBarState', false, { root: true })
        if (response.status == 500) {
          context.commit('SET_ERROR_MESSAGE', response.data.message)
          return
        }
      })
  },
}
