import http from './Client'

export function getAllPlaylists(mediaId) {
  const mediaIdParam = (mediaId ? '?media_id=' + mediaId : '')

  return http.get('/playlist/all' + mediaIdParam)
}

export function createPlaylist(name) {
  return http.post('/playlist/create', {
    name,
  })
}

export function deletePlaylist(playlistId) {
  if (!playlistId) {
    return false
  }

  return http.delete(`/playlist/delete/${playlistId}`)
}

export function addPlaylistItem(playlistId, mediaId) {
  if (!playlistId || !mediaId) {
    return false
  }

  return http.post('/playlist/add', {
    media_id: mediaId,
    playlist_id: playlistId,
  })
}

export function deletePlaylistItem(playlistId, mediaId) {
  if (!playlistId || !mediaId) {
    return
  }

  const route = `/playlist/${playlistId}/delete/${mediaId}`
  return http.delete(route)
}
