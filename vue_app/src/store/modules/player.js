import { getCurrentPathFromURL } from '@/services/GlobalFunctions'

import YoutubePlayerManager from '../../services/YoutubePlayerManager'

export const namespaced = true
export const state = {
  guidIndexMap: {},
  currentIndexKey: false,
  currentPlayingGuid: false,
}

export const mutations = {
  SET_CURRENT_INDEX_KEY(state, guidIndexKey) {
    state.currentIndexKey = guidIndexKey
  },
  UPDATE_GUID_INDEX(state, { guidIndexKey, guidIndex }) {
    state.guidIndexMap[guidIndexKey] = guidIndex
  },
}

export const actions = {
  playGuid({ state, rootState, commit, dispatch }, guid) {
    const currentPath = getCurrentPathFromURL()
    const pathGuidIndex = state.guidIndexMap[currentPath]

    if (currentPath !== state.currentIndexKey && pathGuidIndex) {
      commit('SET_CURRENT_INDEX_KEY', currentPath)
      dispatch('updateGuidIndex', {
        guidIndexKey: currentPath,
        guidIndex: pathGuidIndex,
      })

      YoutubePlayerManager.setGuidIndex(pathGuidIndex)
    }

    YoutubePlayerManager.playGuid(guid, null, 'click-to-play')
  },
  stop() {
    YoutubePlayerManager.stopPlayingGuid()
  },
  setCurrentIndex({ commit }, guidIndexKey) {
    commit('SET_CURRENT_INDEX_KEY', guidIndexKey)
  },
  updateGuidIndex({ commit }, { guidIndexKey, guidIndex }) {
    commit('UPDATE_GUID_INDEX', { guidIndexKey, guidIndex })
    commit('SET_CURRENT_INDEX_KEY', guidIndexKey)
  },
  updateGuidVideoMap({}, map) {
    YoutubePlayerManager.updateGuidVideoMap(map)
  },
  updateGuidData({ dispatch }, { guidIndexKey, mediaItems }) {
    if (!mediaItems) {
      throw new Error('mediaItems are required')
    }

    let guidIndex = []

    // @ TODO we need to clean up this map or it'll build up with duplicate guids
    const guidVideoMap = YoutubePlayerManager.guidVideoMap

    mediaItems.forEach((item) => {
      guidIndex.push(item.guid)

      guidVideoMap[item.guid] = {
        videoId: item.index ?? item.videoId,
        title: item.title,
        thumbnail: item.thumbnail,
        id: item.id ?? item.media_id ?? item.mediaId,
      }
    })

    YoutubePlayerManager.setGuidIndex(guidIndex)
    dispatch('updateGuidIndex', { guidIndexKey, guidIndex })
    dispatch('updateGuidVideoMap', guidVideoMap)
  },
}
