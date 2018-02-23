/* global _ */
import * as types from '../mutation-types';

const state = {
  videos: [],
  profiles: []
};

const getters = {
  profileByHash(state) {
    return (hash) => {
      return state.profiles[hash];
    }
  },
  profiles(state) {
    return state.profiles;
  },
  videos(state) {
    return state.videos;
  },
};

const actions = {
  fetchUserProfile({ commit }, hash) {
    axios.get('/api/media/profile/' + hash).then((resp) => {
      if(resp.status === 200) {
        let params = {
          hash,
          profile:resp.data.collection.youtube
        };

        commit(types.COLLECTION_ADD_PROFILE, params);
      }
    });
  },
  update({ commit }) {
    axios.get('/api/media/collection').then((resp) => {
      if (resp.status === 200) {
        const { collection } = resp.data;
        commit(types.COLLECTION_UPDATE, { collection });
        window._authed = true;
      }
    }).catch(error => {
      window._authed = false;
    });
  },
  discover({ commit }, { type, videoId }) {
    const self = this;
    axios.post('/api/media/discover', {
      type,
      videoId,
    }).then((resp) => {
      if (resp.status === 200) {
        self.dispatch('collection/update');
      }
    });
  },
  toss({ commit }, { type, mediaId }) {
    const self = this;
    axios.get(`/api/media/toss?type=${type}&mediaId=${mediaId}`).then((resp) => {
      if (resp.status === 200) {
        commit(types.COLLECTION_TOSS, mediaId);
        // self.dispatch('collection/update');
      }
    });
  },
};

const mutations = {
  [types.COLLECTION_ADD_PROFILE](state, { hash, profile}) {
    console.log("added profile for " + hash);
    console.log(profile);
    //bail if already fetched
    if(state.profiles[hash]) return;

    state.profiles[hash] = profile;

    console.log(state);
  },
  [types.COLLECTION_UPDATE](state, { collection }) {
    state.videos = collection.youtube;
  },
  [types.COLLECTION_TOSS](state, mediaId) {
    const index = _.findIndex(state.videos, video => video.id === mediaId);

    state.videos.splice(index, 1);
  },
};

const namespaced = true;
export default {
  namespaced,
  state,
  getters,
  actions,
  mutations,
};
