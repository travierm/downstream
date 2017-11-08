
export const fetchCollection = ({commit}) => {
  commit('startFetch');

  axios.post('/api/test').then(() => {
    commit('endFetch', 'success');
  }).catch(() => {
    commit('endFetch', 'error');
  });
};
