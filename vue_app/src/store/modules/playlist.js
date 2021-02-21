import Vue from 'vue'
import { getAllPlaylists } from '../../services/api/PlaylistService'

export const namespaced = true
export const state = {
  playlists: [],
  selectedPlaylist: false,
}

export const mutations = {
  SET_PLAYLISTS(state, playlists) {
    Vue.set(state, 'playlists', playlists)
  },
  SET_SELECTED_PLAYLIST(state, playlistId) {
    state.selectedPlaylist = playlistId
  }
}

export const getters = {}

export const actions = {
  setSelectedPlaylist(context, playlistId) {
    context.commit('SET_SELECTED_PLAYLIST', playlistId)
  },
  getAll(context, mediaId = false) {
    return getAllPlaylists(mediaId)
      .then(resp => {
        const items = resp.data.items
        if (items) {
          context.commit('SET_PLAYLISTS', items)
        }
      })
      .catch(err => {
        console.error(err)
      })
  }
}
