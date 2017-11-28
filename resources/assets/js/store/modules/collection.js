import * as types from '../mutation-types';

const state = {
  videos:[]
};

const getters = {
};

const actions = {
  update({commit}) {
    axios.get('/api/media/collection').then((resp) => {
      if(resp.status == 200) {
        var collection = resp.data.collection;
        commit(types.COLLECTION_UPDATE, {collection});
      }
    });
  },
  discover({commit}, {type, videoId}) {
    var self = this;
    axios.post('/api/media/discover', {
      type:type,
      videoId:videoId
    }).then((resp) => {
      if(resp.status == 200) {
        self.dispatch('collection/update');
      }
    });
  },
  collect() {

  },
  toss({commit}, {type, mediaId}) {
    let self = this;
    axios.get('/api/media/toss?type=' + type + '&mediaId=' + mediaId).then((resp) => {
      if(resp.status == 200) {
        commit(types.COLLECTION_TOSS, mediaId);
        //self.dispatch('collection/update');
      }
    });
  }
};

const mutations = {
  [types.COLLECTION_UPDATE](state, {collection}) {
    state.videos = collection.youtube;
  },
  [types.COLLECTION_TOSS](state, mediaId) {
    const index = _.findIndex(state.videos, (video) => {
      return video.id == mediaId;
    })

    state.videos.splice(index, 1);
  }
};

const namespaced = true;
export default {
  namespaced,
  state,
  getters,
  actions,
  mutations
};
