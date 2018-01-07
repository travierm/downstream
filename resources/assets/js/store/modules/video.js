import * as types from '../mutation-types';
import YTPlayer from 'yt-player';
import MobileDetect from 'mobile-detect';
import _ from 'lodash';

const md = new MobileDetect(window.navigator.userAgent);
const isMobile = md.mobile();

const state = {
  volume: 75,
  isPlaying: false,
  registeredVideos: [],
  previousVideo: false,
  currentVideo: false,
  nextVideo: false,
};

const getters = {
  volume(state) {
    return state.volume;
  },
  videos(state) {
    return state.videos;
  },
  isPlaying(state) {
    return state.isPlaying;
  },
};

const actions = {
  register({ commit }, {
    id, vid, element, options,
  }) {
    if (isMobile) {
      options.autoplay = true;
    }

    const player = new YTPlayer(`#${element}`, options);
    player.load(vid);
    if (isMobile) {
      player.setVolume(0);
      player.pause();
    }

    commit(types.REGISTER_VIDEO, { id, player, commit });
  },
  registerEventAction({ commit }, {id, eventType, callback}) {
    commit(types.REGISTER_VIDEO_EVENT_ACTION, {id, eventType, callback});
  },
  destory({ commit }, id) {
    commit(types.DESTROY_VIDEO, id);
  },
  pause({ commit }, id) {
    commit(types.UPDATE_CURRENT_VIDEO, id);
    commit(types.PAUSE_VIDEO, id);
  },
  pauseCurrent({ commit, state }, id) {
    commit(types.PAUSE_VIDEO, state.currentVideo.id);
  },
  play({ commit }, id) {
    commit(types.UPDATE_CURRENT_VIDEO, id);
    commit(types.PLAY_VIDEO);
    commit(types.QUEUE_NEXT_VIDEO, commit);
  },
  playPrevious({ commit, state }) {
    commit(types.UPDATE_CURRENT_VIDEO, state.previousVideo.id);
    commit(types.PLAY_VIDEO);
    commit(types.QUEUE_NEXT_VIDEO, commit);
  },
  playNext({ commit }) {
    commit(types.UPDATE_CURRENT_VIDEO, state.nextVideo.id);
    commit(types.PLAY_VIDEO);
    commit(types.QUEUE_NEXT_VIDEO, commit);
  },
  updateVolume({ commit }, volumeInt) {
    commit(types.UPDATE_VIDEO_VOLUME, volumeInt);
  },
  startQueue({ commit }) {
    commit(types.START_VIDEO_QUEUE, commit);
  },
};

const mutations = {
  [types.UPDATE_VIDEO_VOLUME](state, volumeInt) {
    state.volume = volumeInt;
    if(state.currentVideo)
      state.currentVideo.player.setVolume(volumeInt);
  },
  [types.START_VIDEO_QUEUE](state, commit) {
    if (!state.currentVideo) {
      commit(types.UPDATE_CURRENT_VIDEO, state.registeredVideos[0].id);
      commit(types.PLAY_VIDEO);
    } else {
      commit(types.PLAY_VIDEO);
    }
  },
  [types.REGISTER_VIDEO](state, { id, player, commit }) {
    player.setVolume(state.volume);
    player.on('playing', () => {
      if (state.currentVideo.id !== id) {
        commit(types.UPDATE_CURRENT_VIDEO, id);
        commit(types.UPDATE_VIDEO_VOLUME, state.volume);
        commit(types.QUEUE_NEXT_VIDEO, commit);
        commit(types.UPDATE_PLAYING_STATUS, true);
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
    state.isPlaying = false;
    state.currentVideo.player.pause();
  },
  [types.UPDATE_PLAYING_STATUS](state, status) {
    state.isPlaying = status;
  },
  [types.PLAY_VIDEO](state) {
    state.isPlaying = true;
    const player = state.currentVideo.player;
    player.on('error', (err) => {
      console.log(err);
    });
    player.setVolume(state.volume);
    player.play();
  },
  [types.QUEUE_NEXT_VIDEO](state, commit) {
    // @MILLION
    state.currentVideo.player.on('ended', () => {
      commit(types.UPDATE_CURRENT_VIDEO, state.nextVideo.id);
      commit(types.PLAY_VIDEO);
      commit(types.QUEUE_NEXT_VIDEO, commit);
    });
  },
  [types.UPDATE_CURRENT_VIDEO](state, id) {
    const index = _.findIndex(state.registeredVideos, video => video.id == id);
    const video = state.registeredVideos[index];

    if (index === -1) {
      throw new Error(`Could update to video:${id}`);
    }

    if (state.currentVideo && state.currentVideo.id !== id) {
      state.currentVideo.player.pause();
    }

    let nextVideoIndex = parseInt(state.registeredVideos.indexOf(video)) + 1;
    let previousVideoIndex = parseInt(state.registeredVideos.indexOf(video)) - 1;
    if (nextVideoIndex >= state.registeredVideos.length) {
      nextVideoIndex = 0;
      previousVideoIndex = state.registeredVideos.length - 1;
    }

    state.currentVideo = video;
    state.nextVideo = state.registeredVideos[nextVideoIndex];
    state.previousVideo = state.registeredVideos[previousVideoIndex];
  },
  [types.REGISTER_VIDEO_EVENT_ACTION](state, {id, eventType, callback}) {
    const index = _.findIndex(state.registeredVideos, video => video.id == id);
    const video = state.registeredVideos[index];

    if(video.player) {
      if(_.isArray(eventType)) {
        _.forEach(eventType, (type) => {
          video.player.on(type, callback);
        });
      }else{
        video.player.on(eventType, callback);
      }
    }else{
      throw new Error("Can not register event for undefined video");
    }
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
