import http from './Client'

export function createWaitListSignup(email, textResponse) {
  return http.post('/waitlist/signup', {
    email,
    textResponse,
  })
}

export function registerUser(displayName, email, password, inviteCode) {
  return http.post('/user/register', {
    display_name: displayName,
    email: email,
    password,
    invite_code: inviteCode,
  })
}
