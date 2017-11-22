import Vue from 'vue';
import Vuex from 'vuex';
import * as mutations from './mutations';
import * as actions from './actions';
import * as getters from './getters';
import collection from './modules/collection';
import frontpage from './modules/frontpage';
import media from './modules/media';

Vue.use(Vuex);

const state = {
  collection:[],
  api: {
    fetching:false,
    fetchStatus:null
  },
  mediaService: {
    players:[]
  }
};

export default new Vuex.Store({
  modules: {
    media,
    collection,
    frontpage
  },
  mutations,
  getters,
  actions,
  state
});
