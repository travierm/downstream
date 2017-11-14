import Vue from 'vue';
import Vuex from 'vuex';
import * as mutations from './mutations';
import * as actions from './actions';
import * as getters from './getters';

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
  mutations,
  getters,
  actions,
  state
});
