import http from './Client'

export async function getMediaByVideoIndex(videoIndex) {
  return http.get('/video/' + videoIndex)
}

export async function getAutofixByMediaId(mediaId) {
  return http.get('/video/autofix/' + mediaId)
}
