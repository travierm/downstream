/**
 * Media Module
 *
 * An interface for handling Media
 * VideoPlayCard will register its own media payload with this modules
 */

import _ from 'lodash';
import * as types from '../mutation-types';
import cache from '../../services/Cache';
import YouTubeVideoPlayer from '../../services/YouTubeVideoPlayer';

const videoPlayer = new YouTubeVideoPlayer();
const initPreloadedVideos = 5;

//set true to disable preloading
//speed improvement doesn't justify all the requests
let didPreload = true;
let preloadTimeout = false;
const isMobile = window._isMobile;

const state = {
  index: [],
  player: videoPlayer,
  current: false,
  history: [],
  //0 - 100
  volume: cache.get('mediaPlayerVolume', 100)
};


videoPlayer.setVolume(state.volume);

const getters = {
  playerVolumeLevel(state) {
    return state.volume;
  },
  isPlaying(state) {
    return videoPlayer.getPlayerState();
  }
};

const actions = {
  // Commits element to index
  resetState({ commit }) {
    videoPlayer.resetState();
    commit(types.UPDATE_CURRENT_VIDEO, false);
  },
  /* 
    Each video component has a unqiue sessionId 

    indexAdd adds a new sessionId to the video index
    the video index is the maintains the postion of videos from top-bottom
  */
  indexAdd({ commit, dispatch }, sessionId) {
    commit(types.INDEX_ADD, sessionId);

    //preload logic
    //disabled for now
    if(!didPreload) {
      if(preloadTimeout) {
        clearTimeout(preloadTimeout);
      }

      preloadTimeout = setTimeout(() => {
        dispatch('preloadIndex');
      }, 500)
    }
  },
  /*
    stores sessionId and video params
    doesn't load videos until needed
  */
  videoAdd({ commit, dispatch }, { sessionId, videoId, options }) {
    if(isMobile) {
      //@TODO mute player and then start autoplaying somehow
      options.fullscreen = false;
    }

    videoPlayer.registerVideo(sessionId, videoId, options);

    if(options.autoplay) {
      dispatch('play', sessionId);
    }
  },
  reset({ dispatch}, sessionId) {
    if(!sessionId) {
      sessionId = state.current;
    }

   videoPlayer.stopVideo(sessionId);
  },
  pause({ commit, state, dispatch}, sessionId) {
    console.log("DOING PAUSE");
    if(!sessionId) {
      sessionId = state.current;
    }

    //pause video
    videoPlayer.pauseVideo(sessionId);

    videoPlayer.registerEvent(sessionId, ['playing'], () => {
      if(state.current == sessionId) {
        return;
      }

      dispatch('updateCurrent', sessionId);
      dispatch('play', sessionId);
    });
  },
  /**
   * updateCurrent
   * 
   * @param sessionId 
   */
  updateCurrent({ commit, dispatch}, sessionId) {
    if(state.current) {
      //pause previously playing video
      dispatch('reset', state.current);
    }

    commit(types.UPDATE_CURRENT_VIDEO, sessionId);
  },
  preload({ commit, state, dispatch}, sessionId) {

    videoPlayer.preloadVideo(sessionId);

    videoPlayer.registerEvent(sessionId, ['playing'], () => {
      if(state.current == sessionId) {
        return;
      }
      dispatch('updateCurrent', sessionId);
      dispatch('play', sessionId);
    });

    videoPlayer.registerEvent(sessionId, ['ended'], () => {
      const nextVideoIndex = getNextVideoId(state.index, sessionId);
      dispatch('play', nextVideoIndex);
    });
  },
  play({ commit, state, dispatch }, sessionId) {
    if(!sessionId) {
      sessionId = state.current;
    }else{
      //update current video
      dispatch('updateCurrent', sessionId);
      //update history
      commit(types.MEDIA_HISTORY_ADD, sessionId);
    }

    const started = videoPlayer.playVideo(sessionId);

    if(!started) {
      //try to play video again if it failed
      console.error("Could not play video. Trying again in a sec")
      setTimeout(() => {
        videoPlayer.playVideo(sessionId);
      }, 500)
    }else{
      //update GA
      let videoId = videoPlayer.findVideo(sessionId).videoId;

      gtag('event', 'play', {
        'event_category' : 'Media',
        'event_label' : videoId
      });
    }

    //play next video once ended
      videoPlayer.registerEvent(sessionId, ['ended'], () => {
        console.log('playing next');
        const nextVideoIndex = getNextVideoId(state.index, sessionId);
        dispatch('play', nextVideoIndex);
      });

      videoPlayer.registerEvent(sessionId, ['playing'], () => {
        if(state.current !== sessionId) {
          dispatch('updateCurrent', sessionId);
        }
      });
  },
  updateVolume({ commit, dispatch, state}, volumeLevel) {
    //update state volume
    commit(types.UPDATE_VOLUME_LEVEL, volumeLevel);

    //sync player service volume
    videoPlayer.setVolume(volumeLevel);

    cache.set('mediaPlayerVolume', volumeLevel);

    if(state.current) {
      //update current playing video volume
      videoPlayer.updatePlayerVolume(state.current);
    }
  },
  //master bar actions
  historyBack({ commit, state, dispatch }) {
    if(state.current) {
      const nextId = getPreviousVideoId(state.index, state.current);
      if(nextId) {
        dispatch('play', nextId);
      }
    }
  },
  indexNext({ commit, state, dispatch }) {
    if(!state.current) {
      dispatch('startQueue');
      return;
    }

    const nextId = getNextVideoId(state.index, state.current);
    if(nextId) {
      dispatch('play', nextId);
    }
  },
  startQueue({commit, state, dispatch}) {
    dispatch('play', state.index[0]);
  },
  preloadIndex({ commit, state }) {

    let loadIndex = [];
    for(var i = 0; i < initPreloadedVideos; i++) {
      loadIndex.push(state.index[i]);
    }

    _.forEach(loadIndex, (video) => {
      videoPlayer.preloadVideo(video);
    });
  },
  registerEvent({}, {sessionId, eventType, callback}) {
    videoPlayer.registerEvent(sessionId, eventType, callback);
  }
};

const mutations = {
  [types.MEDIA_HISTORY_ADD](state, sessionId) {
    state.history.push(sessionId);
  },
  [types.INDEX_ADD](state, sessionId) {
    state.index.push(sessionId);
  },
  [types.INDEX_SHUFFLE](state) {
    state.index = _.shuffle(state.index);
  },
  [types.INDEX_REMOVE](state, sessionId) {
    const index = state.index.indexOf(sessionId);

    if(!index) {
      console.error("Can not remove sessionId from index");
      return;
    }

    state.index.splice(index, 1);
  },
  [types.INDEX_REPLACE](state, index) {
    state.index = index;
  },
  [types.INDEX_CLEAR](state) {
    state.index = [];
  },
  [types.COLLECTION_UPDATE](state, items) {
    state.fetched.collection = items;
  },
  [types.UPDATE_CURRENT_VIDEO](state, sessionId) {
    state.current = sessionId;
  },
  [types.UPDATE_VOLUME_LEVEL](state, volumeLevel) {
    state.volume = volumeLevel;
  }
}; 

function getPreviousVideoId(index, currentId) {
  if(index.indexOf(currentId) == 0) {
    return index[index.length - 1];
  }

  return index[index.indexOf(currentId) - 1];
}

function getNextVideoId(index, currentId) {
  if(index.indexOf(currentId) >= (index.length - 1)) {
    return index[0];
  }

  return index[index.indexOf(currentId) + 1];
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
