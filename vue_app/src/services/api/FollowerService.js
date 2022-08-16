import http from './Client'

function fetchFollowage() {
  return http.get(`/followage`)
}

function unFollow(followerId) {
  return http.delete(`/unfollow/${followerId}`)
}

function follow(followerId) {
  return http.put(`/follow/${followerId}`)
}

export default { fetchFollowage, follow, unFollow }
