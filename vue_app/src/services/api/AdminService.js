import http from './Client'

export function getAdminDashboard() {
  return http.get('/admin/dashboard')
}
