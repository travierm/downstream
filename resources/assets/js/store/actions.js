import YouTubePlayer from "youtube-player";

export const fetchCollection = ({commit}) => {
  commit('startFetch');

  /*axios.post('/api/media/add').then((resp) => {

    if(resp.data) {
      //commit('updateCollection', resp.data);
      commit('endFetch', 'success');
      return;
    }

    commit('endFetch', 'false');
  }).catch(() => {
    commit('endFetch', 'error');
  });*/
};

//Master Bar
