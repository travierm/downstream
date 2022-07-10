import http from './Client'

export function getAuthorizeUrl() {
  return http.get('/spotify/authorize')
}

export function connectSpotify(code, state) {
  return http.post('/spotify/connect', {
    code,
    state,
  })
}
