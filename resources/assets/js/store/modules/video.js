import * as types from '../mutation-types';
import YTPlayer from 'yt-player';

const state = {
  registeredVideos: [],
  currentVideo: false,
};

const getters = {
  videos(state) {
    return state.videos;
  },
};

const actions = {
  register({ commit }, {
    id, vid, element, options,
  }) {
    const player = new YTPlayer(`#${element}`, options);
    player.load(vid);
    player.setVolume(100);

    commit(types.REGISTER_VIDEO, { id, player });
  },
  destory({ commit }, id) {
    commit(types.DESTROY_VIDEO, id);
  },
  pause({commit}, id) {
    commit(types.UPDATE_CURRENT_VIDEO, id);
    commit(types.PAUSE_VIDEO, id);
  },
  play({ commit }, id) {
    commit(types.UPDATE_CURRENT_VIDEO, id);
    commit(types.PLAY_VIDEO, id);
  },
};

const mutations = {
  [types.REGISTER_VIDEO](state, { id, player }) {
    state.registeredVideos.push({
      id,
      player,
    });
  },
  [types.DESTROY_VIDEO](state, id) {
    state.registeredVideos = state.registeredVideos.filter(video => video.id !== id);
  },
  [types.PAUSE_VIDEO](state, id) {
    state.currentVideo.player.pause();
  },
  [types.PLAY_VIDEO](state, id) {
    state.currentVideo.player.play();
  },
  [types.UPDATE_CURRENT_VIDEO](state, id) {
    const index = _.findIndex(state.registeredVideos, video => video.id == id);
    const video = state.registeredVideos[index];

    if (index == -1) {
      throw new Error(`Could update to video:${id}`);
    }

    if (state.currentVideo && state.currentVideo.id !== id) {
      state.currentVideo.player.pause();
    }

    state.currentVideo = video;
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
