import http from './Client'

async function playedMedia(mediaId = false) {
  if (!mediaId) {
    return
  }

  try {
    return http.get('/ana/media/play/' + mediaId)
  } catch (error) {
    console.error(error)
  }
}

export default { playedMedia }
