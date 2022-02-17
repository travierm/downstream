import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

import * as auth from './modules/auth'
import * as player from './modules/player'
import * as search from './modules/search'
import * as playlist from './modules/playlist'
import * as discover from './modules/discover'
import * as collection from './modules/collection'

import Analytics from '../services/api/AnalyticsService'

export default new Vuex.Store({
  state: {
    showLoadingBar: false,
    playlistDrawerStatus: false,
    navDrawerStatus: false,
    mediaStats: {},
  },
  mutations: {
    SET_MEDIA_STATS(state, obj) {
      state.mediaStats = obj
    },
    SET_PLAYLIST_DRAWER_STATUS(state, boolean) {
      state.playlistDrawerStatus = boolean
    },
    SET_LOADING_BAR_STATE(state, boolean) {
      state.showLoadingBar = boolean
    },
    TOGGLE_NAV_DRAWER_STATUS(state) {
      state.navDrawerStatus = !state.navDrawerStatus
    },
  },
  actions: {
    async getMediaStats(context) {
      const stats = await Analytics.getMediaStats()

      if (stats.data) {
        context.commit('SET_MEDIA_STATS', stats.data)
      }
    },
    setPlaylistDrawerStatus(context, boolean) {
      context.commit('SET_PLAYLIST_DRAWER_STATUS', boolean)
    },
    setLoadingBarState(context, boolean) {
      context.commit('SET_LOADING_BAR_STATE', boolean)
    },
    toggleNavDrawerStatus(context) {
      context.commit('TOGGLE_NAV_DRAWER_STATUS')
    }
  },
  modules: {
    auth,
    player,
    search,
    playlist,
    discover,
    collection,
  },
})
