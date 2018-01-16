import * as types from '../mutation-types';
import YTPlayer from 'yt-player';
import MobileDetect from 'mobile-detect';
import _ from 'lodash';

const isMobile = window._isMobile;

const state = {
  volume: 75,
  isPlaying: false,
  registeredVideos: [],
  loadedVideos:[],
  currentVideo: false,
  previousId: false,
  nextId: false,
};

function findById(array, id) {
  return _.find(array, {media: { id: id}});
}

const getters = {
  volume(state) {
    return state.volume;
  },
  isPlaying(state) {
    return state.isPlaying;
  },
};

const actions = {
  register({ commit }, {media, elementId, options}) {
    const player = new YTPlayer("#" + elementId, options);

    commit(types.REGISTER_VIDEO, { media, player });
  },
  pause({ commit }) {
    commit(types.PAUSE_CURRENT_VIDEO);
  },
  play({ commit }, mediaId) {
    commit(types.LOAD_VIDEO, mediaId);
    commit(types.UPDATE_CURRENT_VIDEO, mediaId);
    commit(types.PLAY_CURRENT_VIDEO);
    commit(types.QUEUE_NEXT_VIDEO, commit);
  },
  registerEventAction({ commit }, { id, eventType, callback }) {
    commit(types.REGISTER_VIDEO_EVENT_ACTION, { id, eventType, callback });
  },
};

const mutations = {
  [types.REGISTER_VIDEO](state, video) {
    state.registeredVideos.push(video);
  },
  [types.LOAD_VIDEO](state, mediaId) {

    if(findById(state.loadedVideos, mediaId)) {
      //video loaded already
      console.info('video already loaded');
      return;
    }

    let video = findById(state.registeredVideos, mediaId);
    video.player.load(video.media.index);
    video.player.setVolume(state.volume);

    state.loadedVideos.push(video);
  },
  [types.UPDATE_CURRENT_VIDEO](state, mediaId) {
    let video = findById(state.loadedVideos, mediaId);
    if(!video) {
      console.error("Could not update to video " + mediaId);
      return;
    }

    //set next ids for queue
    let nextVideoIndex = parseInt(state.registeredVideos.indexOf(video)) + 1;
    let previousVideoIndex = parseInt(state.registeredVideos.indexOf(video)) - 1;
    if(previousVideoIndex == -1) {
      previousVideoIndex = state.registeredVideos.length - 1;
    }

    if(nextVideoIndex >= state.registeredVideos.length) {
      //next video index is out of range
      nextVideoIndex = 0;
      previousVideoIndex = state.registeredVideos.length - 1;
    }

    state.nextId = state.registeredVideos[nextVideoIndex].media.id;
    state.previousId = state.registeredVideos[previousVideoIndex].media.id;
    state.currentVideo = video;

  },
  [types.QUEUE_NEXT_VIDEO](state, commit) {
    state.currentVideo.player.on('ended', () => {
      commit(types.LOAD_VIDEO, state.nextId);
      commit(types.UPDATE_CURRENT_VIDEO, state.nextId);
      commit(types.PLAY_CURRENT_VIDEO);
      commit(types.QUEUE_NEXT_VIDEO, commit);
    });
  },
  [types.PAUSE_CURRENT_VIDEO](state) {
    state.currentVideo.player.pause();
  },
  [types.PLAY_CURRENT_VIDEO](state) {
    state.currentVideo.player.play();
  },
  [types.REGISTER_VIDEO_EVENT_ACTION](state, { id, eventType, callback }) {
    const index = _.findIndex(state.registeredVideos, video => video.media.id == id);
    const video = state.registeredVideos[index];

    if (video.player) {
      if (_.isArray(eventType)) {
        _.forEach(eventType, (type) => {
          video.player.on(type, callback);
        });
      } else {
        video.player.on(eventType, callback);
      }
    } else {
      throw new Error('Can not register event for undefined video');
    }
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
