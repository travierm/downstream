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
  activity: [{ date: '2026-10-17', users: 2, collections: 5 }],
  recent_users: [], recent_collections: [], updated_at: '2026-10-17T12:00:00Z',
})
const flush = () => new Promise(resolve => setTimeout(resolve, 0))
const mount = () => shallowMount(AdminView, { stubs: ['apexchart'] })

describe('admin dashboard', () => {
  beforeEach(() => vi.clearAllMocks())

  it('renders totals, empty feeds, and activity from the API', async () => {
    getAdminDashboard.mockResolvedValue({ data: dashboard() })
    const wrapper = mount()
    await flush()
    expect(wrapper.text()).toContain('42')
    expect(wrapper.text()).toContain('New users will appear here')
    expect(wrapper.vm.chartSeries[0].data).toEqual([5])
    expect(wrapper.vm.activityTotals).toEqual({ users: 2, collections: 5 })
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
