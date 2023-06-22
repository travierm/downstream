import http from './Client'

async function playedMedia(mediaId = false, playType) {
  if (!mediaId) {
    return
  }

  try {
    console.info('Pinging server about played media', mediaId, playType)

    return http.post('/ana/media/play/' + mediaId, {
      playType,
    })
  } catch (error) {
    throw Error
  }
}

async function getMediaStats() {
  return http.get('/ana/media/stats')
}

export default { playedMedia, getMediaStats }
