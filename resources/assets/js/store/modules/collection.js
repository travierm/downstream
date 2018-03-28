/* global _ */
import * as types from '../mutation-types';

const state = {
  collectionAccess: true
};

const getters = {
};

const actions = {
  update({ commit, dispatch }) {
    axios.get('/api/media/collection').then((resp) => {
      if (resp.status === 200) {
        const { items } = resp.data;
        commit(types.COLLECTION_UPDATE, { item });

        dispatch('media/register', { items:collection }, {root: true});
        window._authed = true;
      }
    }).catch(error => {
      window._authed = false;
    });
  },
  discover({ commit }, { type, videoId }) {
    const self = this;
    return axios.post('/api/media/discover', {
      type,
      videoId,
    });
  },
  toss({ commit }, { type, mediaId }) {
    const self = this;
    axios.get(`/api/media/toss?type=${type}&mediaId=${mediaId}`).then((resp) => {
      if (resp.status === 200) {
        commit(types.COLLECTION_TOSS, mediaId);
      }else if(resp.status === 401) {
        commit(types.COLLECTION_ACCESS, false)
      }
    });
  },
};

const mutations = {
  [types.COLLECTION_ACCESS](state, status) {
    state.collectionAccess = status;
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
