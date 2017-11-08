import Vue from 'vue';
import Vuex from 'vuex';
import * as mutations from './mutations';
import * as actions from './actions';

Vue.use(Vuex);

const state = {
  collection:[],
  api: {
    fetching:false,
    fetchStatus:null
  }
};

export default new Vuex.Store({
  mutations,
  actions,
  state
});
