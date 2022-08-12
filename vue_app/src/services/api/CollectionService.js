import http from './Client'

async function fetchCollection(playlistId = false, userHash) {
  const playlistParam = playlistId ? '?playlist_id=' + playlistId : ''
  const userHashParam = userHash ? `/${userHash}` : ''
  return http.get('/collection' + userHashParam + playlistParam)
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
