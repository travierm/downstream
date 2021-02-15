import _ from 'lodash'
import CollectionService from '@/services/api/CollectionService'

export const namespaced = true
export const state = {
  searchQuery: '',
  hash: window.localStorage.getItem('collectionHash'),
  collection: JSON.parse(window.localStorage.getItem('collection')),
}

export const mutations = {
  SET_SEARCH_QUERY(state, query) {
    state.searchQuery = query
  },
  SHUFFLE_COLLECTION(state) {
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
  guidIndex(state) {
    if (!state.collection) {
      return []
    }

    return state.collection.map(item => {
      return item.guid
    })
  },
  collectionSearchResults(state) {
    const searchQuery = state.searchQuery

    if (!searchQuery) {
      return state.collection
    }

    return state.collection.filter(video => {
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
    const guidIndex = context.getter.guidIndex

    if (guidIndex) {
      context.dispatch('player/setGuidIndex', guidIndex, { root: true })
    }
  },
  async fetchCollection({ commit, rootState, state }) {
    if (!rootState.auth.token) {
      return
    }

    const response = await CollectionService.fetchCollection()
    if (response.data) {
      if (response.data.hash !== state.hash) {
        console.log('Collection hash does not match. Updating local state!')

        commit('UPDATE_COLLECTION', response.data)
      }
    }
  },
}
