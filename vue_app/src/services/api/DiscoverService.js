import http from './Client'

async function getSimilarTracksByVideoId(videoId) {
  return http.get(`/discover/track/${videoId}`)
}

export default { getSimilarTracksByVideoId }
