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

function getActiveUsers() {
  return http.get(`/users/active`)
}

export default { fetchFollowage, follow, unFollow, getActiveUsers }
