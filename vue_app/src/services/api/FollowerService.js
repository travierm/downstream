import http from './Client'

export function fetchFollowage() {
  return http.get(`/followage`)
}

export function unFollow(followerId) {
  return http.delete(`/unfollow/${followerId}`)
}

export function follow(followerId) {
  return http.put(`/follow/${followerId}`)
}

export function getActiveUsers() {
  return http.get(`/users/active`)
}

export function getFollowingActivites() {
  return http.get(`/following/activity`).then((res) => res.data)
}
