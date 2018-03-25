import _ from 'lodash';
import * as types from '../mutation-types';

const state = {
  count: 0
};

const getters = {
	currentCount(state) {
		return state.count;
	}
};

const actions = {
  addOne({ commit }) {
    commit(type.ADD_ONE);
  }
};

const mutations = {
  [types.ADD_ONE](state) {
    state.counter += 1;
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};