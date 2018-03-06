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
  return _.find(array, {media: { sessionId: id}});
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
    let playerOpts = {elementId, options};
    commit(types.REGISTER_VIDEO, { media, playerOpts });
  },
  unregisterAll({ commit }) {
    commit(types.UNREGISTER_ALL);
  },
  pause({ commit }) {
    commit(types.PAUSE_CURRENT_VIDEO, commit);
  },
  play({ commit }, sessionId) {
    commit(types.LOAD_VIDEO, { sessionId, commit});
    commit(types.UPDATE_CURRENT_VIDEO, sessionId);
    commit(types.PLAY_CURRENT_VIDEO);
    commit(types.QUEUE_NEXT_VIDEO, commit);
  },
  unmute({ commit }) {
    commit(types.UNMUTE_CURRENT_VIDEO);
  },
  destroy({ commit }, sessionId) {
    commit(types.DESTROY_VIDEO, sessionId);
  },
  registerEventAction({ commit }, { id, eventType, callback }) {
    commit(types.REGISTER_VIDEO_EVENT_ACTION, { id, eventType, callback });
  },
  updateVolume({ commit }, volumeInt) {
    commit(types.UPDATE_VIDEO_VOLUME, volumeInt);
  },
};

const mutations = {
  [types.UNMUTE_CURRENT_VIDEO](state) {
    state.currentVideo.player.unMute();
  },
  [types.UNREGISTER_ALL](state) {
    state.registeredVideos = [];
    state.currentVideo = false;
  },
  [types.UPDATE_VIDEO_VOLUME](state, volumeInt) {
    state.volume = volumeInt;
    if (state.currentVideo) { state.currentVideo.player.setVolume(volumeInt); }
  },
  [types.DESTROY_VIDEO](state, sessionId) 
  {
    /*state.registeredVideos = _.remove(state.registeredVideos, (n) => {
      return n.media.sessionId !== sessionId;
    })

    state.loadedVideos = _.remove(state.registeredVideos, (n) => {
      return n.media.sessionId !== sessionId;
    })*/
  },
  [types.REGISTER_VIDEO](state, video) {
    state.registeredVideos.push(video);
  },
  [types.LOAD_VIDEO](state, { sessionId, commit}) {

    if(findById(state.loadedVideos, sessionId)) {
      //video loaded already
      console.info('video already loaded');
      return;
    }

    let video = findById(state.registeredVideos, sessionId);
    if(!video) {
      console.error("Could not LOAD " + sessionId);
      return;
    }

    if(!$("#" + video.playerOpts.elementId).length) {
      console.error("Video player anchor element [" + video.playerOpts.elementId + "] undefined");
      return; 
    }
    video.playerOpts.options.autoplay = false;
    video.playerOpts.options.height = $(`#${video.playerOpts.elementId}_media`).height();
      console.log($('#' + this.id + '_thumbnail').height());
    //video.playerOpts.options.height += 555;
    video.player = new YTPlayer("#" + video.playerOpts.elementId, video.playerOpts.options);

    video.player.on('error', (err) => {
      console.log("VIDEO ERROR")
      console.error(err);

    })

    video.player.on('unplayable', () => {
      console.log("Unplayable");
    })

    video.player.on('unstarted', () => {
      console.log("Can't play video: unstarted");
    })

    window.video = video.player;
    
    if(window._isMobile) {
      console.log('is mobile and muting')
      video.player.mute();
    }

    video.player.load(video.media.index, false);
    //video.player.pause();


    video.player.on('playing', () => {
      if(state.currentVideo.media.sessionId !== video.media.sessionId) {
        //video started playing without being the current video
        console.info("started playing while not current video");
        commit(types.UPDATE_CURRENT_VIDEO, video.media.sessionId);
        commit(types.QUEUE_NEXT_VIDEO, commit);
        state.isPlaying = true;
        video.player.setVolume(state.volume);

        setTimeout(() => {
          video.player.unMute();
        }, 5000)
      }
    });

    console.log("loaded");
    state.loadedVideos.push(video);

    if(video.events) {
      video.events.forEach(function(event) {
        commit(types.REGISTER_VIDEO_EVENT_ACTION, event);
      });
    }
  },
  [types.UPDATE_CURRENT_VIDEO](state, sessionId) {
    let video = findById(state.loadedVideos, sessionId);
    if(!video) {
      console.error("Could not update to video " + sessionId);
      return;
    }

    if(state.currentVideo) {
      try {
         state.currentVideo.player.pause();
         state.currentVideo.player.seek(0);
      } catch(e) {
        console.info("try failed");
        console.log(state.currentVideo);
      }
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

    state.nextId = state.registeredVideos[nextVideoIndex].media.sessionId;

    if(state.registeredVideos[previousVideoIndex].media.sessionId) {
      state.previousId = state.registeredVideos[previousVideoIndex].media.sessionId;
    }else{
      console.info(state.registeredVideos);
    }

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
      commit(types.LOAD_VIDEO, { sessionId: state.nextId, commit});
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
  /*
    PLAY CURRENT VIDEO
  */
  [types.PLAY_CURRENT_VIDEO](state) {
    state.currentVideo.player.play();
  },
  /*
    Register Video
   */
  [types.REGISTER_VIDEO_EVENT_ACTION](state, { id, eventType, callback }) {
    const index = _.findIndex(state.loadedVideos, video => video.media.sessionId == id);
    const video = state.loadedVideos[index];

    if(!video) {
      const index = _.findIndex(state.registeredVideos, video => video.media.sessionId == id);
      const unloadedVideo = state.registeredVideos[index];

      if(unloadedVideo) {
        if(!unloadedVideo.events) {
          unloadedVideo.events = [];
        }

        unloadedVideo.events.push({
          id,
          eventType,
          callback
        });

        return;
      }
    }


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
  mutations
};
