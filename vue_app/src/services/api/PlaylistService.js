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
    return
  }

  return http.delete(`/playlist/delete/${playlistId}`)
}

export function addPlaylistItem(playlistId, mediaId) {
  if (!playlistId || !mediaId) {
    return
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

  return http.post('/playlist/add', {
    media_id: mediaId,
    playlist_id: playlistId,
  })
}
