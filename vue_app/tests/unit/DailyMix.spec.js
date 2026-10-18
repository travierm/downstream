import { shallowMount } from '@vue/test-utils'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import DailyMixView from '../../src/components/views/DailyMixView.vue'
import NavDrawer from '../../src/components/Shared/NavDrawer.vue'
import NavBar from '../../src/components/Shared/NavBar.vue'
import DiscoverService from '../../src/services/api/DiscoverService'

vi.mock('../../src/services/api/DiscoverService', () => ({ default: { getDailyMix: vi.fn() } }))
const flush = () => new Promise(resolve => setTimeout(resolve, 0))
const videos = [{ videoId: 'a', title: 'Track', guid: 'guid_a' }]
function mountMix() {
  return shallowMount(DailyMixView, { global: { mocks: { $store: { dispatch: vi.fn().mockResolvedValue() } } } })
}

describe('Daily Mix access', () => {
  beforeEach(() => vi.clearAllMocks())

  it('offers Daily Mix in the signed-in navigation and opens its route', async () => {
    const store = {
      state: { navDrawerStatus: true, auth: { user: { display_name: 'Listener', type: 'user' } } },
      getters: { 'auth/loggedIn': true }, dispatch: vi.fn(), commit: vi.fn(),
    }
    const push = vi.fn()
    const wrapper = shallowMount(NavDrawer, { global: { mocks: { $store: store, $router: { push } } } })
    expect(wrapper.text()).toContain('Daily Mix')
    const item = wrapper.findAllComponents({ name: 'VListItem' }).find(item => item.text().includes('Daily Mix'))
    item.vm.$emit('click')
    await wrapper.vm.$nextTick()
    expect(push).toHaveBeenCalledWith('/daily-mix')
    expect(store.commit).toHaveBeenCalledWith('SET_NAV_DRAWER_STATUS', false)
    wrapper.unmount()
  })

  it('keeps the navigation menu available on mobile', () => {
    const wrapper = shallowMount(NavBar, { global: {
      mocks: {
        $vuetify: { display: { smAndUp: false } },
        $store: { state: { collection: {}, mediaStats: {}, showLoadingBar: false }, getters: { 'auth/loggedIn': true } },
      },
      stubs: { SearchBar: true, 'router-link': true },
    } })
    expect(wrapper.find('[aria-label="Open navigation menu"]').exists()).toBe(true)
    wrapper.unmount()
  })

  it.each([
    ['recent collection', true, [{ created_at: '2026-10-17 12:00:00', deleted_at: null }], true],
    ['30-day boundary', true, [{ created_at: '2026-09-18T12:00:00Z' }], true],
    ['older collection', true, [{ created_at: '2026-09-18T11:59:59Z' }], false],
    ['empty collection', true, [], false],
    ['removed track', true, [{ created_at: '2026-10-17T12:00:00Z', deleted_at: '2026-10-18' }], false],
    ['unknown date', true, [{ title: 'Track' }], false],
    ['signed out', false, [{ created_at: '2026-10-17T12:00:00Z' }], false],
  ])('shows the header Daily Mix shortcut appropriately for %s', (_, loggedIn, collection, visible) => {
    const now = vi.spyOn(Date, 'now').mockReturnValue(Date.parse('2026-10-18T12:00:00Z'))
    const wrapper = shallowMount(NavBar, { global: {
      mocks: {
        $vuetify: { display: { smAndUp: false } },
        $store: { state: { collection: { collection }, mediaStats: {}, showLoadingBar: false }, getters: { 'auth/loggedIn': loggedIn } },
      },
      stubs: {
        SearchBar: true, 'router-link': true,
        VTooltip: { template: '<div><slot name="activator" :props="{}" /><slot /></div>' },
      },
    } })
    const shortcut = wrapper.find('[aria-label="Open Daily Mix"]')
    expect(shortcut.exists()).toBe(visible)
    if (visible) {
      expect(shortcut.attributes('to')).toBe('/daily-mix')
      expect(wrapper.text()).toContain('Daily Mix — recommendations from your recent collections')
    }
    wrapper.unmount()
    now.mockRestore()
  })

  it('shows loading then registers returned tracks with the player', async () => {
    let complete
    DiscoverService.getDailyMix.mockReturnValue(new Promise(resolve => { complete = resolve }))
    const wrapper = mountMix()
    await wrapper.vm.$nextTick()
    expect(wrapper.text()).toContain('Finding tracks')
    complete({ data: { videos } })
    await flush()
    expect(wrapper.vm.results).toEqual(videos)
    expect(wrapper.vm.loading).toBe(false)
    expect(wrapper.vm.$store.dispatch).toHaveBeenCalledWith('player/updateGuidData', { guidIndexKey: '/daily-mix', mediaItems: videos })
    wrapper.unmount()
  })

  it('shows a useful empty state for an account without a mix', async () => {
    DiscoverService.getDailyMix.mockResolvedValue({ data: { videos: [], message: 'Add some tracks to your collection.' } })
    const wrapper = mountMix()
    await flush()
    expect(wrapper.text()).toContain('Add some tracks to your collection.')
    expect(wrapper.find('[to="/collection"]').exists()).toBe(true)
    wrapper.unmount()
  })

  it('offers retry after a provider failure and avoids duplicate clicks while loading', async () => {
    DiscoverService.getDailyMix.mockRejectedValueOnce({ status: 503, data: { message: 'Please try again shortly.' } })
      .mockResolvedValueOnce({ data: { videos } })
    const wrapper = mountMix()
    await flush()
    expect(wrapper.text()).toContain('Please try again shortly.')
    const request = wrapper.vm.loadDailyMix()
    await wrapper.vm.loadDailyMix()
    await request
    expect(DiscoverService.getDailyMix).toHaveBeenCalledTimes(2)
    expect(wrapper.vm.errorMessage).toBe(false)
    expect(wrapper.vm.results).toEqual(videos)
    wrapper.unmount()
  })
})
