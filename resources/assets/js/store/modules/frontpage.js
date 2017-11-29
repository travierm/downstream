import * as types from '../mutation-types';

const state = {
  videos: [],
};

const getters = {

};

const actions = {
  update({ commit }, videos) {
    commit(types.FRONTPAGE_UPDATE, videos);
  },
  remove({ commit }, id) {
    commit(types.FRONTPAGE_REMOVE, id);
  },
};

const mutations = {
  [types.FRONTPAGE_UPDATE](state, videos) {
    state.videos = videos;
  },
  [types.FRONTPAGE_REMOVE](state, id) {
    state.videos = state.videos.filter(video => video.id !== id);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
