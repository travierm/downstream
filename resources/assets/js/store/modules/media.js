import _ from 'lodash';
import * as types from '../mutation-types';
import YouTubeVideoPlayer from '../../services/YouTubeVideoPlayer';

const videoPlayer = new YouTubeVideoPlayer();
const initPreloadedVideos = 5;

let didPreload = false;
let preloadTimeout = false;
const isMobile = window._isMobile;

const state = {
  index: [],
  player: videoPlayer,
  current: false,
  history: [],
  fetched: {
    collection: []
  }
};

const getters = {
  isPlaying(state) {
    return videoPlayer.getPlayerState();
  },
  collection(state) {
    return state.fetched.collection;
  },
};

const actions = {
  // Commits element to index
  indexAdd({ commit, dispatch }, sessionId) {
    commit(types.INDEX_ADD, sessionId);

    if(!didPreload) {
      if(preloadTimeout) {
        clearTimeout(preloadTimeout);
      }

      preloadTimeout = setTimeout(() => {
        dispatch('preloadIndex');
      }, 500)
    }
  },
  videoAdd({ commit }, { sessionId, videoId, options }) {
    if(isMobile) {
      //@TODO mute player and then start autoplaying somehow
      options.fullscreen = false;
    }

    videoPlayer.registerVideo(sessionId, videoId, options);
  },
  pause({ commit, state, dispatch}, sessionId) {
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
  updateCurrent({ commit, dispatch}, sessionId) {
    if(state.current) {
      //pause previously playing video
      dispatch('pause', state.current);
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

    videoPlayer.playVideo(sessionId);

    //play next video once ended
    videoPlayer.registerEvent(sessionId, ['ended'], () => {
      consl('playing next');
      const nextVideoIndex = getNextVideoId(state.index, sessionId);
      dispatch('play', nextVideoIndex);
    });
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
  },
  // AJAX CALLS
  getCollection({ commit }) {
    axios.get('/api/media/collection').then((resp) => {
      if (resp.status === 200) {
        // items refers to media items in a users collection
        const { items } = resp.data;
        commit(types.COLLECTION_UPDATE, items);
      }
    }).catch((error) => {
      if (error) throw error;
    });
  },
};

const mutations = {
  [types.MEDIA_HISTORY_ADD](state, sessionId) {
    state.history.push(sessionId);
  },
  [types.INDEX_ADD](state, sessionId) {
    state.index.push(sessionId);
  },
  [types.COLLECTION_UPDATE](state, items) {
    state.fetched.collection = items;
  },
  [types.UPDATE_CURRENT_VIDEO](state, sessionId) {
    state.current = sessionId;
  }
};


function getPreviousVideoId(index, currentId) {
  if(index.indexOf(currentId) >= (index.length - 1)) {
    return index[0];
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
