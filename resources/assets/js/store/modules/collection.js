import * as types from '../mutation-types'

const state = {

};

const getters = {

};

const actions = {
  discover({commit}, type, videoId) {
    axios.post('/api/media/discover', {
      type:type,
      videoId:videoId
    }).then((resp) => {
      if(resp.status == 200) {
        console.log(resp);
        //commit(types.MEDIA_COLLECTED, )
      }
    });
  },
  collect() {

  },
  toss() {

  }
};

const mutations = {
  [types.MEDIA_COLLECTED](state, {m}) {
    console.log(state);
  }
};

export default {
  state,
  getters,
  actions,
  mutations
};
