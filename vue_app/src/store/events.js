import store from './index'

export async function fetchInitUserData() {
  store.dispatch('playlist/getAll')
  store.dispatch('collection/fetchCollection')
  store.dispatch('getMediaStats')
}

document.addEventListener('visibilitychange', function () {
  if (document.visibilityState === 'visible') {
    store.dispatch('getMediaStats')
  }
})
