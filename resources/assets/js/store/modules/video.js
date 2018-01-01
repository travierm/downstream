import * as types from '../mutation-types';
import YTPlayer from 'yt-player';
import MobileDetect from 'mobile-detect';

var md = new MobileDetect(window.navigator.userAgent);
const isMobile = md.mobile();

const state = {
  registeredVideos: [],
  previousVideo: false,
  currentVideo: false,
  nextVideo: false,
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
    if(isMobile) {
      console.log('Im mobile!');
      options.autoplay = true;
    }
    const player = new YTPlayer(`#${element}`, options);
    if(isMobile) {
      player.setVolume(0);
    }
    player.load(vid);

    commit(types.REGISTER_VIDEO, { id, player, commit});
  },
  destory({ commit }, id) {
    commit(types.DESTROY_VIDEO, id);
  },
  pause({commit}, id) {
    commit(types.UPDATE_CURRENT_VIDEO, id);
    commit(types.PAUSE_VIDEO, id);
  },
  pauseCurrent({commit, state}, id) {
    commit(types.PAUSE_VIDEO, state.currentVideo.id);
  },
  play({ commit }, id) {
    commit(types.UPDATE_CURRENT_VIDEO, id);
    commit(types.PLAY_VIDEO);
    commit(types.QUEUE_NEXT_VIDEO, commit);
  },
  playPrevious({commit, state}) {
    commit(types.UPDATE_CURRENT_VIDEO, state.previousVideo.id);
    commit(types.PLAY_VIDEO);
    commit(types.QUEUE_NEXT_VIDEO, commit);
  },
  playNext({commit}) {
    commit(types.UPDATE_CURRENT_VIDEO, state.nextVideo.id);
    commit(types.PLAY_VIDEO);
    commit(types.QUEUE_NEXT_VIDEO, commit);
  },
  startQueue({commit}) {
    commit(types.START_VIDEO_QUEUE, commit);
  }
};

const mutations = {
  [types.START_VIDEO_QUEUE](state, commit) {
    if(!state.currentVideo) {
      commit(types.UPDATE_CURRENT_VIDEO, state.registeredVideos[0].id);
      commit(types.PLAY_VIDEO);
    }else{
      commit(types.PLAY_VIDEO);
    }
  },
  [types.REGISTER_VIDEO](state, { id, player, commit }) {
    player.on('playing', () => {
      if(state.currentVideo.id !== id) {
        console.log('updating current video');
        commit(types.UPDATE_CURRENT_VIDEO, id);
        commit(types.QUEUE_NEXT_VIDEO, commit);
      }
    });


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
  [types.PLAY_VIDEO](state) {
    const player = state.currentVideo.player;
    player.setVolume(100);
    player.play();
  },
  [types.QUEUE_NEXT_VIDEO](state, commit) {
    //@MILLION$
    state.currentVideo.player.on('ended', () => {
      console.log('playing next video!');
      commit(types.UPDATE_CURRENT_VIDEO, state.nextVideo.id);
      commit(types.PLAY_VIDEO);
    })
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

    let nextVideoIndex = parseInt(state.registeredVideos.indexOf(video)) + 1;
    let previousVideoIndex = parseInt(state.registeredVideos.indexOf(video)) - 1;
    if(nextVideoIndex >= state.registeredVideos.length) {
      nextVideoIndex = 0;
      previousVideoIndex = state.registeredVideos.length - 1;
    }

    state.currentVideo = video;
    state.nextVideo = state.registeredVideos[nextVideoIndex];
    state.previousVideo = state.registeredVideos[previousVideoIndex];
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
