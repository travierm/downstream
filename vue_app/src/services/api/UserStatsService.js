import http from './Client';

export function getUserStats() {
  return http.get(`/user/stats`)
}
