/* global _ */
import SID from 'shortid';
import { generateElementId } from '../../services/Utils';
import * as types from '../mutation-types';

SID.characters('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$@');

const state = {
  collectionAccess: true,
  items:[]
};

const getters = {
};

const actions = {
  update({ commit }, randomize) {
    var randomFlag = "";
    if(randomize) {
      randomFlag="/random"
    }


    return axios.get('/api/media/collection' + randomFlag).then((resp) => {
      if (resp.status === 200) {
        // items refers to media items in a users collection
        const { items } = resp.data;
        commit(types.COLLECTION_UPDATE, items);
      }
    }).catch((error) => {
      if (error) throw error;
    });
  },
  discover({ commit }, { type, videoId, spotifyId }) {
    const self = this;
    return axios.post('/api/media/discover', {
      type,
      videoId,
      spotifyId
    });
  },
  toss({ commit }, { type, mediaId }) {
    const self = this;
    
    return axios.get(`/api/media/toss?type=${type}&mediaId=${mediaId}`).then((resp) => {
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
  [types.COLLECTION_UPDATE](state, items) {
    _.map(items, (item) => {
      item.sessionId = generateElementId();
      return item;
    });

    state.items = items;
  },
  [types.COLLECTION_TOSS](state, mediaId) {
    const index = _.findIndex(state.items, video => video.id === mediaId);

    state.items.splice(index, 1);
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
