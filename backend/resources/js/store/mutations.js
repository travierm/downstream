export const startFetch = (state) => {
  state.api.fetching = true;
};

export const endFetch = (state, status) => {
  state.api.fetching = false;
  state.api.fetchStatus = status;
};

export const updateCollection = (state, videos) => {
  state.collection = videos;
};
