import http from './Client'

export function getDisable() {
  return http.get('/spotify/disable')
}

export function getAuthorizeUrl() {
  return http.get('/spotify/authorize')
}

export function runSpotifySync() {
  return http.get('/spotify/run-sync')
}

export function connectSpotify(code, state) {
  return http.post('/spotify/connect', {
    code,
    state,
  })
}
