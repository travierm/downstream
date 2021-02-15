import http from './Client'

async function fetchCollection() {
  return http.get('/collection')
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
