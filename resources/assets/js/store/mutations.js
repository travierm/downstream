
export const startFetch = (state) => {
  state.api.fetching = true;
  console.log('making api request...');
};

export const endFetch = (state, status) => {

};
