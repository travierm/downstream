import _ from 'lodash'
import CollectionService from '@/services/api/CollectionService'

export const namespaced = true
export const state = {
  shuffled: false,
  searchQuery: '',
  searchQueryUpdates: 0,
  hash: window.localStorage.getItem('collectionHash'),
  // collection: JSON.parse(window.localStorage.getItem('collection')),
  collection: [],
}

export const mutations = {
  SET_SEARCH_QUERY(state, query) {
    state.searchQuery = query
    state.searchQueryUpdates++
  },
  SHUFFLE_COLLECTION(state) {
    state.shuffled = true
    state.collection = _.shuffle(state.collection)
  },
  REMOVE_COLLECTION_ITEM(state, mediaId) {
    state.collection = _.reject(state.collection, { media_id: mediaId })
  },
  UPDATE_COLLECTION(state, data) {
    const { hash, items } = data

    state.hash = hash
    state.collection = Object.freeze(items)

    window.localStorage.setItem('collectionHash', hash)
    window.localStorage.setItem('collection', JSON.stringify(items))
  },
}

export const getters = {
  searchQueryUpdates() {
    return state.searchQueryUpdates
  },
  guidIndex(state) {
    if (!state.collection) {
      return []
    }

    return state.collection.map((item) => {
      return item.guid
    })
  },
  collectionSearchResults(state) {
    const searchQuery = state.searchQuery

    if (!searchQuery) {
      return state.collection
    }

    return state.collection.filter((video) => {
      return video.title.toLowerCase().includes(searchQuery.toLowerCase())
    })
  },
}

export const actions = {
  shuffle(context) {
    context.commit('SHUFFLE_COLLECTION')
    context.dispatch('player/setGuidIndex', context.getters.guidIndex, {
      root: true,
    })
  },
  collectItem(commit, videoId) {
    return CollectionService.collectItem(videoId)
  },
  removeItem({ commit }, mediaId) {
    commit('REMOVE_COLLECTION_ITEM', mediaId)

    return CollectionService.removeItem(mediaId)
  },
  updateGuidIndex(context) {
    const guidIndex = context.getters.guidIndex

    if (guidIndex) {
      context.dispatch('player/setGuidIndex', guidIndex, { root: true })
    }
  },
  async fetchCollection({ commit, rootState, dispatch }, playlistId = false) {
    if (!rootState.auth.token) {
      return
    }

    dispatch('setLoadingBarState', true, { root: true })

    return CollectionService.fetchCollection(playlistId)
      .then((response) => {
        commit('UPDATE_COLLECTION', response.data)
        dispatch(
          'player/updateGuidData',
          { guidIndexKey: '/collection', mediaItems: response.data.items },
          { root: true }
        )

        dispatch('setLoadingBarState', false, { root: true })
      })
      .catch(() => {
        dispatch('setLoadingBarState', false, { root: true })
      })
  },
}
