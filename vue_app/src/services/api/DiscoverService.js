import http from './Client'

async function getSimilarTracksByVideoId(videoId) {
  return http.get(`/discover/track/${videoId}`)
}

async function getDailyMix() {
  return http.get(`/daily-mix`)
}

export default { getSimilarTracksByVideoId, getDailyMix }
