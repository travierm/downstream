import _ from 'lodash';
import * as types from '../mutation-types';
import YouTubePlayer from 'youtube-player';

const state = {
  videos:[],
  playingVideo:false,
  currentVideo:false
};

const getters = {

};

const actions = {
  playVideo({commit}, {id}) {
    commit(types.MEDIA_UPDATE_CURRENT_VIDEO, {id});
    commit(types.MEDIA_PLAY_VIDEO);
  },
  pauseVideo({commit}, {id}) {
    commit(types.MEDIA_PAUSE_VIDEO, {id});
  },
  registerVideo({commit}, {id, player}) {

    commit(types.MEDIA_REGISTER_VIDEO, {
      id,
      player
    });
  }
};

const mutations = {
  [types.MEDIA_PLAY_VIDEO](state) {
    let video = state.currentVideo;

    video.player.playVideo();
    state.playing = true;
  },
  [types.MEDIA_PAUSE_VIDEO](state, {id}) {
    let video = state.currentVideo;
    if(video.id == id) {
      video.player.pauseVideo();
      state.playing = false;
    }
  },
  [types.MEDIA_UPDATE_CURRENT_VIDEO](state, {id}) {
    const index = _.findIndex(state.videos, (video) => {
      return video.id == id;
    });

    let video = state.videos[index];
    console.log(video);
    state.currentVideo = video;
  },
  [types.MEDIA_REGISTER_VIDEO](state, {id, player}) {
    state.videos.push({
      id,
      player
    });
  }
};

export default {
  namespaced:true,
  state,
  getters,
  actions,
  mutations
};
