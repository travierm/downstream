import _ from 'lodash';
import * as types from '../mutation-types';
import { inArray } from '../../services/core';

const state = {
  index: []
};

const getters = {};

const actions = {
  registerOnIndex({ commit }, { sessionId }) {
    commit(types.PLAYER_INDEX_ADD, sessionId);
  },
};

const mutations = {
  /*
    Player Index Add

    adds media item to player index which acts as the list to render these items  
  */
  [types.PLAYER_INDEX_ADD](state, sessionId) {
    //sessionId is not already indexed
    if(!inArray(sessionId, state.index)) {
      state.index.push(sessionId);
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