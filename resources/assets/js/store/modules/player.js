//Media Player Engine
import _ from 'lodash';
import * as types from '../mutation-types';

const state = {
    index: [],
    items: [],
    currentId: false
};

const getters = {
};

const actions = {
    register({ commit }, item) {
        commit(types.PLAYER_REGISTER_ITEM, item)
    },
    updateCurrent({ commit, state }, sessionId) {
        const previousCurrentId = state.currentId;

        if(previousCurrentId) {
            //do something with video player before this
            let item = findBySessionId(state.items, previousCurrentId);

            item.callbackHandler((video) => {
                video.pause();
            });
        }

        commit(types.PLAYER_UPDATE_CURRENT, sessionId);
    },
    play({ commit, state, dispatch }, sessionId) {
        let item = findBySessionId(state.items, sessionId);

        dispatch('updateCurrent', sessionId);

        item.callbackHandler((video) => {
            video.play();
        })
    }
};

const mutations = {
    [types.PLAYER_RESET](state) {
        state.items = [];
        state.index = [];
    },
    [types.PLAYER_REGISTER_ITEM](state, item) {
        state.items.push(item);
        state.index.push(item.sessionId);
    },
    [types.PLAYER_UPDATE_CURRENT](state, sessionId) {
        state.currentId = sessionId;
    }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};

function findBySessionId(items, sessionId) {
    return _.find(items, {
        sessionId: sessionId
    });
}