import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

import * as auth from './modules/auth'
import * as player from './modules/player'
import * as search from './modules/search'
import * as playlist from './modules/playlist'
import * as collection from './modules/collection'

export default new Vuex.Store({
  state: {
    showLoadingBar: false,
    playlistDrawerStatus: false,
  },
  mutations: {
    SET_PLAYLIST_DRAWER_STATUS(state, boolean) {
      state.playlistDrawerStatus = boolean
    },
    SET_LOADING_BAR_STATE(state, boolean) {
      state.showLoadingBar = boolean
    },
  },
  actions: {
    setPlaylistDrawerStatus(context, boolean) {
      context.commit('SET_PLAYLIST_DRAWER_STATUS', boolean)
    },
    setLoadingBarState(context, boolean) {
      context.commit('SET_LOADING_BAR_STATE', boolean)
    },
  },
  modules: {
    auth,
    player,
    search,
    playlist,
    collection,
  },
})
