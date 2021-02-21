import http from './Client'

async function fetchCollection(playlistId = false) {
  const playlistParam = (playlistId ? '?playlist_id=' + playlistId : '')

  return http.get('/collection' + playlistParam)
}

async function collectItem(videoId) {
  return http.post('/media/collect', {
    videoId,
  })
}

async function pushItem(mediaId) {
  return http.post('/media/push/' + mediaId)
}

async function removeItem(itemId) {
  return http.delete(`/media/collection/${itemId}`)
}

export default { collectItem, pushItem, removeItem, fetchCollection }
