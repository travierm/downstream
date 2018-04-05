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
  play({ commit, state, dispatch }, sessionId) {
    //update current video
    dispatch('updateCurrent', sessionId);

    videoPlayer.playVideo(sessionId);

    //play next video once ended
    videoPlayer.registerEvent(sessionId, ['ended'], () => {
      const nextVideoIndex = getNextVideoId(state.index, sessionId);
      dispatch('play', nextVideoIndex);
    });
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


function getNextVideoId(index, currentId) {
  consl(index.length);
  consl(index.indexOf(currentId));
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
