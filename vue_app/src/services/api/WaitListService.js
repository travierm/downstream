import http from './Client'

export function createWaitListSignup(email, textResponse) {
  return http.post('/waitlist/signup', {
    email,
    textResponse,
  })
}
