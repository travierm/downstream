import http from './Client'

export async function getMediaByVideoIndex(videoIndex) {
  return http.get('/video/' + videoIndex)
}
