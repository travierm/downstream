//Media Player Engine
import _ from 'lodash';
import * as types from '../mutation-types';
import cache from '../../services/Cache';
import { arrayNextIndex } from '../../services/Utils';

const state = {
    index: [],
    items: [],
    currentId: false,
    volume: cache.get('mediaPlayerVolume', 100),
    playing: false
};

const getters = {
    isPlaying(state) {
        return state.playing;
    },
    getVolume(state) {
        return state.volume;
    }
};

const actions = {
    reset({ commit }) {
        commit(types.PLAYER_RESET);
    },
    register({ commit }, item) {
        commit(types.PLAYER_REGISTER_ITEM, item)
    },
    deregister({ commit }, item) {
        commit(types.PLAYER_DEREGISTER_ITEM, item)
    },
    updateVolume({ commit, state }, volume) {
        //update state
        commit(types.PLAYER_UPDATE_VOLUME, volume);

        if(state.currentId) {
            let media = findBySessionId(state.items, state.currentId);

            media.callbackHandler((self) => {
                self.play(state.volume);
            })
        }
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
        commit(types.PLAYER_PLAYING, true);

        console.log(sessionId);
        console.log(item);

        item.callbackHandler((self) => {
            self.play(state.volume);
        })
    },
    playCurrent({ commit, state }) {
        if(!state.currentId) {
            console.error("Could not play current because currentId is not set");
            return;
        }

        const currentId = state.currentId;
        let media = findBySessionId(state.items, currentId);
        commit(types.PLAYER_PLAYING, true);

        media.callbackHandler((self) => {
            self.play(state.volume);
        })
    },
    pauseCurrent({ commit, state }) {
        if(!state.currentId) {
            console.error("Could not pause current because currentId is not set");
            return;
        }

        const currentId = state.currentId;
        let media = findBySessionId(state.items, currentId);
        commit(types.PLAYER_PLAYING, false);

        media.callbackHandler((self) => {
            self.pause();
        })
    },
    indexStepForward({ state, dispatch }) {
        if(!state.currentId) {
            dispatch('updateCurrent', state.index[0]);
            dispatch('playCurrent');
            return true;
        }

        const nextIndex = arrayNextIndex(state.index, state.currentId, "+");
        dispatch('updateCurrent', nextIndex);
        dispatch('playCurrent');
    },
    indexStepBackward({ state, dispatch }) {
        if(!state.currentId) {
            dispatch('updateCurrent', state.index[state.index.length - 1]);
            dispatch('playCurrent');
            return true;
        }

        const nextIndex = arrayNextIndex(state.index, state.currentId, "-");
        dispatch('updateCurrent', nextIndex);
        dispatch('playCurrent');
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
    [types.PLAYER_PLAYING](state, status) {
        state.playing = status;
    },
    [types.PLAYER_INDEX_REPLACE](state, newIndex) {
        state.index = newIndex;
    },
    [types.PLAYER_UPDATE_VOLUME](state, volume) {
        state.volume = volume;
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