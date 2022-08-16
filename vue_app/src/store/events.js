import store from './index'

export async function fetchInitUserData() {
  store.dispatch('auth/getUser')
  store.dispatch('playlist/getAll')
  store.dispatch('collection/fetchCollection')
  store.dispatch('getMediaStats')
  store.dispatch('follower/getFollowage')
}

document.addEventListener('visibilitychange', function () {
  if (document.visibilityState === 'visible') {
    store.dispatch('getMediaStats')
  }
})
