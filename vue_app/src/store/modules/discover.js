import Vue from 'vue'
import DiscoverService from '../../services/api/DiscoverService'

const freshnessMs = 60 * 1000
// Scope pending work and freshness to the Vuex state and signed-in session.
const sessions = new WeakMap()

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
  similarTracks: state => videoId => state.similarTracks[videoId],
}

export const actions = {
  getSimilarTracks(context, videoId) {
    const user = context.rootState.auth?.user
    const token = context.rootState.auth?.token
    let session = sessions.get(context.state)
    if (!session || session.user !== user || session.token !== token) {
      session = { user, token, pending: new Map(), fetchedAt: new Map() }
      sessions.set(context.state, session)
    }
    const isCurrentSession = () => sessions.get(context.state) === session && context.rootState.auth?.user === user && context.rootState.auth?.token === token
    const updatePlayer = items => context.dispatch(
      'player/updateGuidData',
      { guidIndexKey: `/discover/track/${videoId}`, mediaItems: items },
      { root: true }
    )

    if (session.pending.has(videoId)) {
      return session.pending.get(videoId)
    }
    const fetchedAt = session.fetchedAt.get(videoId)
    if (fetchedAt !== undefined && Date.now() - fetchedAt < freshnessMs) {
      context.commit('SET_ERROR_MESSAGE', false)
      return Promise.resolve(updatePlayer(context.state.similarTracks[videoId]))
    }

    if (session.pending.size === 0) {
      context.dispatch('setLoadingBarState', true, { root: true })
    }
    const request = DiscoverService.getSimilarTracksByVideoId(videoId)
      .then(async response => {
        if (!isCurrentSession()) return
        context.commit('SET_ERROR_MESSAGE', false)
        if (Array.isArray(response.data?.items)) {
          context.commit('SET_SIMILAR_TRACKS', { videoId, items: response.data.items })
          await updatePlayer(response.data.items)
          session.fetchedAt.set(videoId, Date.now())
        }
      })
      .catch(response => {
        if (isCurrentSession()) {
          context.commit('SET_ERROR_MESSAGE', response?.data?.message || 'Could not load similar tracks. Please try again.')
        }
      })
      .finally(() => {
        session.pending.delete(videoId)
        if (sessions.get(context.state) === session && session.pending.size === 0) {
          context.dispatch('setLoadingBarState', false, { root: true })
        }
      })
    session.pending.set(videoId, request)
    return request
  },
}
