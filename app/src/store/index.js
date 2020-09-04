import Vue from 'vue'
import Vuex from 'vuex'

import * as auth from './modules/auth';
import { fetchUserCollection }  from '../services/api/CollectionService';

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    collection: []
  },
  mutations: {

  },
  actions: {
    async fetchCollection() {
      let collectionItems = await fetchUserCollection();
    }
  },
  modules: {
    auth
  }
})
