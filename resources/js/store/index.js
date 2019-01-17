import Vue from 'vue';
import Vuex from 'vuex';
import * as mutations from './mutations';
// import * as getters from './getters';

// Modules
// import media from './modules/media';
import player from './modules/player';
import collection from './modules/collection';
import user from './modules/user';

Vue.use(Vuex);

const state = {
  api: {
    fetching: false,
    fetchStatus: null,
  },
};

export default new Vuex.Store({
  modules: {
    user,
    player,
    // media,
    collection,
  },
  mutations,
  state,
});
