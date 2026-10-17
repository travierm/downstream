import http from './Client'

export function registerUser(displayName, email, password, passwordConfirmation, turnstileToken) {
  return http.post('/user/register', {
    display_name: displayName,
    email,
    password,
    password_confirmation: passwordConfirmation,
    'cf-turnstile-response': turnstileToken,
  })
}
