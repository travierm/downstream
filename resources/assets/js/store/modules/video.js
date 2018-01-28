import * as types from '../mutation-types';
import YTPlayer from 'yt-player';
import MobileDetect from 'mobile-detect';
import _ from 'lodash';

const isMobile = window._isMobile;

const state = {
  volume: 50,
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
    commit(types.PAUSE_CURRENT_VIDEO, commit);
  },
  play({ commit }, mediaId) {
    commit(types.LOAD_VIDEO, { mediaId, commit});
    commit(types.UPDATE_CURRENT_VIDEO, mediaId);
    commit(types.PLAY_CURRENT_VIDEO);
    commit(types.QUEUE_NEXT_VIDEO, commit);
  },
  registerEventAction({ commit }, { id, eventType, callback }) {
    commit(types.REGISTER_VIDEO_EVENT_ACTION, { id, eventType, callback });
  },
  updateVolume({ commit }, volumeInt) {
    commit(types.UPDATE_VIDEO_VOLUME, volumeInt);
  },
};

const mutations = {
  [types.UPDATE_VIDEO_VOLUME](state, volumeInt) {
    state.volume = volumeInt;
    if (state.currentVideo) { state.currentVideo.player.setVolume(volumeInt); }
  },
  [types.REGISTER_VIDEO](state, video) {
    state.registeredVideos.push(video);
  },
  [types.LOAD_VIDEO](state, { mediaId, commit}) {

    if(findById(state.loadedVideos, mediaId)) {
      //video loaded already
      console.info('video already loaded');
      return;
    }

    let video = findById(state.registeredVideos, mediaId);
    if(!video) {
      console.error("Could not LOAD " + mediaId);
      return;
    }
    video.player.load(video.media.index);
    video.player.setVolume(state.volume);

    video.player.on('playing', () => {
      if(state.currentVideo.media.id !== video.media.id) {
        //video started playing without being the current video
        console.info("started playing while not current video");
        commit(types.UPDATE_CURRENT_VIDEO, video.media.id);
        commit(types.QUEUE_NEXT_VIDEO, commit);
        state.isPlaying = true;
      }
    });

    state.loadedVideos.push(video);
  },
  [types.UPDATE_CURRENT_VIDEO](state, mediaId) {
    let video = findById(state.loadedVideos, mediaId);
    if(!video) {
      console.error("Could not update to video " + mediaId);
      return;
    }

    if(state.currentVideo) {
      state.currentVideo.player.pause();
      state.currentVideo.player.seek(0);
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
      if(state.currentVideo.player.getState() == "unstarted") {
        //@TODO possible yt-player bug which calls ended event on unstarted video
        //dont queue next video becuase this one didn't play
        //only happens when current video queued in  a player event closure
        return;
      }

      console.info("player ended starting next video");
      commit(types.LOAD_VIDEO, { mediaId: state.nextId, commit});
      commit(types.UPDATE_CURRENT_VIDEO, state.nextId);
      commit(types.QUEUE_NEXT_VIDEO, commit);
      commit(types.PLAY_CURRENT_VIDEO);
    });
  },
  [types.PAUSE_CURRENT_VIDEO](state, commit) {
    if(state.currentVideo) {
      state.currentVideo.player.pause();
    }
  },
  [types.PLAY_CURRENT_VIDEO](state) {
    state.currentVideo.player.play();
  },
  /*
    Register Video
   */
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
