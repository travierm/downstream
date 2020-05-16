import Vue from 'vue'
import Vuex from 'vuex'
import { fetchUserCollection }  from '../services/collection-service';

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    auth: {},
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
  }
})
