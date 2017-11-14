import YouTubePlayer from "youtube-player";

export const fetchCollection = ({commit}) => {
  commit('startFetch');

  axios.post('/api/media/add').then((resp) => {

    if(resp.data) {
      //commit('updateCollection', resp.data);
      commit('endFetch', 'success');
      return;
    }

    commit('endFetch', 'false');
  }).catch(() => {
    commit('endFetch', 'error');
  });
};

//Master Bar
export const registerVideo = ({commit, state}, video) => {

  let player = YouTubePlayer(video.id, {
    videoId: video.vid,
    width:$('#' + video.vid).width()
  });

  state.mediaService.players.push(player);
}
