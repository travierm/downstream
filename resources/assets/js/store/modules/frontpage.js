import * as types from '../mutation-types'

const state = {
  tracks:[]
};

const getters = {

};

const actions = {
  fetch() {
    axios.get('/api/frontpage/all').then((resp) => {
      if(resp.status == 200) {
        console.log(resp);
      }
    });
  }
};

const mutations = {
  set() {

  },
  clear() {

  }
};

export default {
  namespaced:true,
  state,
  getters,
  actions,
  mutations
};
