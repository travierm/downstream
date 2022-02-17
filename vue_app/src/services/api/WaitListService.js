import http from './Client'

export async function createWaitistSignup(email, textResponse) {
  return http.post('/waitlist/signup', {
    email,
    textResponse,
  })
}
