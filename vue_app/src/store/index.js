import Vue from 'vue'
import Vuex from 'vuex'

import * as auth from './modules/auth'
import * as search from './modules/search'
import * as collection from './modules/collection'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
  },
  mutations: {

  },
  actions: {
  },
  modules: {
    auth,
    search,
    collection
  }
})
