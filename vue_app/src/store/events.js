import store from './index'

export async function fetchInitUserData() {
  store.dispatch('playlist/getAll')
  store.dispatch('collection/fetchCollection')
  store.dispatch('getMediaStats')
}
