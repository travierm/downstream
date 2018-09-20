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
    deregister({ commit }, item) {
        commit(types.PLAYER_DEREGISTER_ITEM, item)
    },
    updateCurrent({ commit, state }, sessionId) {
        const previousCurrentId = state.currentId;

        if(previousCurrentId) {
            //do something with video player before this
            let item = findBySessionId(state.items, previousCurrentId);

            item.callbackHandler((video) => {
                console.log("updated current pause")
                try {
                    video.pause();
                } catch (error) {
                    
                }
            });
        }

        commit(types.PLAYER_UPDATE_CURRENT, sessionId);
    },
    play({ commit, state, dispatch }, sessionId) {
        let item = findBySessionId(state.items, sessionId);

        dispatch('updateCurrent', sessionId);

        item.callbackHandler((self) => {
            self.play();
        })
    },
    playCurrent({ state }) {
        if(!state.currentId) {
            console.error("Could not play current because currentId is not set");
            return;
        }

        const currentId = state.currentId;
        let media = findBySessionId(state.items, currentId);

        media.callbackHandler((self) => {
            self.play();
        })

    },
    indexStepForward({ state, dispatch }) {
        if(!state.currentId) {
            dispatch('updateCurrent', state.index[0]);
            dispatch('playCurrent');
            return true;
        }

        arrayNextIndex(state.index, state.currentId, "+");
    },
    indexStepBackward({ state }) {
        if(!state.currentId) {
            dispatch('updateCurrent', state.index[state.index.length - 1]);
            dispatch('playCurrent');
            return true;
        }
    },
    indexReplace({ commit, state }, sessionIndex) {
        commit(types.PLAYER_INDEX_REPLACE, sessionIndex);
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
    [types.PLAYER_DEREGISTER_ITEM](state, item) {
        _.remove(state.items, item);
        _.remove(state.index, item.sessionId);
    },
    [types.PLAYER_UPDATE_CURRENT](state, sessionId) {
        state.currentId = sessionId;
    },
    [types.PLAYER_INDEX_REPLACE](state, newIndex) {
        state.index = newIndex;
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

function arrayNextIndex(array, currentIndex, direction = "+") {
    if(direction !== "+" && direction !== "-") {
        throw new Error("Direction param must be + for forward or - for backward. Neither is given.");
    }

    const arrayLength = array.length;
    const indexPlace = _.findIndex(array, currentIndex);
    console.log(indexPlace);
}
