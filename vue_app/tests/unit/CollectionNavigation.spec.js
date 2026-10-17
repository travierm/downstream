import { createLocalVue, mount } from '@vue/test-utils'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import { describe, expect, it, vi } from 'vitest'
import CollectionView from '../../src/components/views/CollectionView.vue'
import * as collection from '../../src/store/modules/collection'
import * as playlist from '../../src/store/modules/playlist'
import CollectionService from '../../src/services/api/CollectionService'

vi.mock('../../src/services/api/CollectionService', () => ({
  default: { fetchCollection: vi.fn() },
}))

const song = { guid: 'saved-song', media_id: 1, title: 'My saved song' }
const flush = () => new Promise(resolve => setTimeout(resolve, 0))

async function navigateToCollection({ items = [], searchQuery = '', query = {}, from = '/admin', response } = {}) {
  const localVue = createLocalVue()
  localVue.use(Vuex)
  localVue.use(VueRouter)
  CollectionService.fetchCollection.mockReset().mockResolvedValue({ data: { hash: 'hash', items: [song] } })
  if (response) CollectionService.fetchCollection.mockReturnValue(response)
  const store = new Vuex.Store({
    state: { showLoadingBar: false, auth: { token: 'test-token' } },
    actions: { setLoadingBarState: ({ state }, value) => { state.showLoadingBar = value } },
    modules: {
      collection: { ...collection, state: { ...collection.state, collection: items, searchQuery } },
      playlist: { ...playlist, state: { ...playlist.state, selectedPlaylist: 99 } },
      player: { namespaced: true, actions: { setCurrentIndex: vi.fn(), updateGuidData: vi.fn() } },
    },
  })
  const router = new VueRouter({ mode: 'abstract', routes: [
    { path: from, component: { render: h => h('div', 'Other page') } },
    { path: '/collection', component: CollectionView },
  ] })
  await router.push(from)
  const wrapper = mount({ render: h => h('router-view') }, {
    localVue, store, router,
    stubs: {
      CardCol: { template: '<div><slot /></div>' },
      CollectionBar: true,
      YoutubeCard: { props: ['title'], template: '<div class="song">{{ title }}</div>' },
      VLazy: { template: '<div><slot /></div>' },
    },
  })
  await router.push({ path: '/collection', query })
  await flush()
  return { wrapper, store }
}

describe('collection navigation', () => {
  it('keeps visible cards mounted when the navigation refresh returns new playback GUIDs', async () => {
    let resolveResponse
    const response = new Promise(resolve => { resolveResponse = resolve })
    const { wrapper, store } = await navigateToCollection({ items: [song], response })
    try {
      const card = wrapper.find('.song').element
      expect(card.textContent).toContain(song.title)
      const refreshedSong = { ...song, guid: 'new-playback-guid', title: 'Updated title' }
      resolveResponse({ data: { hash: 'hash', items: [refreshedSong] } })
      await flush()

      expect(CollectionService.fetchCollection).toHaveBeenCalledTimes(1)
      expect(store.state.collection.collection).toEqual([refreshedSong])
      expect(wrapper.find('.song').element).toBe(card)
      expect(card.textContent).toContain('Updated title')
    } finally { wrapper.destroy() }
  })

  it.each(['/admin', '/stats', '/users'])('loads saved songs when entering from %s with no collection in memory', async (from) => {
    const { wrapper, store } = await navigateToCollection({ from })
    try {
      expect(wrapper.text()).toContain(song.title)
      expect(wrapper.text()).not.toContain('start your collection')
      expect(store.state.playlist.selectedPlaylist).toBe(false)
      expect(CollectionService.fetchCollection).toHaveBeenCalledWith(false)
    } finally { wrapper.destroy() }
  })

  it('preserves a search explicitly requested in the destination URL', async () => {
    const { wrapper, store } = await navigateToCollection({ query: { search: 'no matching song' } })
    try {
      expect(store.state.collection.collection).toEqual([song])
      expect(store.state.collection.searchQuery).toBe('no matching song')
      expect(wrapper.text()).toContain('No results found')
    } finally { wrapper.destroy() }
  })

  it('loads the playlist requested in the destination URL', async () => {
    const { wrapper, store } = await navigateToCollection({ query: { playlist_id: '42' } })
    try {
      expect(CollectionService.fetchCollection).toHaveBeenCalledWith('42')
      expect(store.state.playlist.selectedPlaylist).toBe('42')
      expect(wrapper.text()).toContain(song.title)
    } finally { wrapper.destroy() }
  })

  it('clears an old search when entering the unfiltered collection from admin', async () => {
    const { wrapper } = await navigateToCollection({ items: [song], searchQuery: 'no matching song' })
    try {
      expect(wrapper.text()).toContain(song.title)
      expect(wrapper.text()).not.toContain('No results found')
    } finally { wrapper.destroy() }
  })
})
