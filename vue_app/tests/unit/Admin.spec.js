import { shallowMount } from '@vue/test-utils'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import AdminView from '../../src/components/views/AdminView.vue'
import { getAdminDashboard } from '../../src/services/api/AdminService'
import { applyMiddleware } from '../../src/router/middleware'
import store from '../../src/store/index'

vi.mock('../../src/services/api/AdminService', () => ({ getAdminDashboard: vi.fn() }))
vi.mock('../../src/store/index', () => ({ default: { state: { auth: { user: null } }, getters: {}, dispatch: vi.fn() } }))

const dashboard = () => ({
  stats: { users: 42, new_users_week: 2, collections: 100, collections_week: 5, unique_songs: 80, plays_week: 20, active_collectors_week: 3 },
  activity: [{ date: '2026-10-16', users: 0, collections: 0, plays: 0 }, { date: '2026-10-17', users: 2, collections: 5, plays: 20 }],
  recent_users: [], recent_collections: [], updated_at: '2026-10-17T12:00:00Z',
})
const flush = () => new Promise(resolve => setTimeout(resolve, 0))
const mount = () => shallowMount(AdminView, {
  stubs: {
    apexchart: { props: ['series', 'options'], template: '<div class="chart-stub" />' },
    'v-simple-table': { template: '<table><slot /></table>' },
  },
})

describe('admin dashboard', () => {
  beforeEach(() => vi.clearAllMocks())

  it('renders statistics and the activity chart from the API', async () => {
    getAdminDashboard.mockResolvedValue({ data: dashboard() })
    const wrapper = mount()
    await flush()
    expect(wrapper.text()).toContain('42')
    expect(wrapper.text()).toContain('Total users')
    expect(wrapper.text()).toContain('Daily activity')
    const charts = wrapper.findAll('.chart-stub')
    expect(charts).toHaveLength(2)
    expect(charts.at(0).props('series')).toEqual([{ name: 'Songs played', data: [0, 20] }])
    expect(charts.at(0).props('options').xaxis.categories).toEqual(['2026-10-16', '2026-10-17'])
    expect(charts.at(1).props('series')).toEqual([
      { name: 'Songs collected', data: [0, 5] },
      { name: 'New users', data: [0, 2] },
    ])
    expect(wrapper.text()).toContain('No registered users.')
    expect(wrapper.vm.activityTotals).toEqual({ users: 2, collections: 5 })
    wrapper.destroy()
  })

  it('shows recently registered users, registration dates, and collection sizes in a table', async () => {
    const data = dashboard()
    data.recent_users = [
      { id: 1, display_name: 'New listener', created_at: '2026-10-17T12:00:00Z', collection_count: 12 },
      { id: 2, display_name: null, created_at: '2026-10-16T12:00:00Z', collection_count: 0 },
    ]
    getAdminDashboard.mockResolvedValue({ data })
    const wrapper = mount()
    await flush()
    const rows = wrapper.findAll('tbody tr')
    expect(rows).toHaveLength(2)
    expect(rows.at(0).findAll('td').wrappers.map(cell => cell.text())).toEqual([
      'New listener', new Date(data.recent_users[0].created_at).toLocaleString(), '12',
    ])
    expect(rows.at(0).find('time').attributes('datetime')).toBe(data.recent_users[0].created_at)
    expect(rows.at(1).text()).toContain('Unnamed user')
    expect(rows.at(1).findAll('td').at(2).text()).toBe('0')
    wrapper.destroy()
  })

  it('allows retry after an error and clears protected data when access is revoked', async () => {
    getAdminDashboard.mockRejectedValueOnce({ status: 500 }).mockResolvedValueOnce({ data: dashboard() }).mockRejectedValueOnce({ status: 403 })
    const wrapper = mount()
    await flush()
    expect(wrapper.text()).toContain('Use Refresh to try again')
    await wrapper.vm.loadDashboard()
    expect(wrapper.vm.dashboard.stats.users).toBe(42)
    expect(wrapper.vm.error).toBe('')
    await wrapper.vm.loadDashboard()
    expect(wrapper.vm.dashboard).toBeNull()
    expect(wrapper.text()).toContain('Administrator access is required')
    wrapper.destroy()
  })

  it('redirects guests and regular users while allowing admins through the route guard', async () => {
    const guards = []
    applyMiddleware({ beforeEach: guard => guards.push(guard) })
    const to = { matched: [{ meta: { requiresAuth: true, requiresAdmin: true } }] }
    const next = vi.fn()
    store.getters['auth/loggedIn'] = false
    await guards[1](to, {}, next)
    expect(next).toHaveBeenLastCalledWith('/')
    store.getters['auth/loggedIn'] = true
    store.state.auth.user = { type: 'user' }
    await guards[1](to, {}, next)
    expect(next).toHaveBeenLastCalledWith('/collection')
    store.state.auth.user = { type: 'admin' }
    await guards[1](to, {}, next)
    expect(next).toHaveBeenLastCalledWith()
  })
})
