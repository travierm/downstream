import * as types from '../mutation-types';

const state = {
  videos: [],
};

const getters = {
  videos(state) {
    return state.videos;
  },
};

const actions = {
  update({ commit }) {
    axios.get('/api/frontpage').then((resp) => {
      if (resp.status === 200) {
        const { videos } = resp.data;
        commit(types.FRONTPAGE_UPDATE, videos);
      }
    });
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
