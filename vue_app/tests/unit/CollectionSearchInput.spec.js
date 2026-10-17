import { mount } from '@vue/test-utils'
import { afterEach, describe, expect, it, vi } from 'vitest'
import CollectionSearchInput from '../../src/components/Collection/CollectionSearchInput.vue'

function mountSearch(dispatch = vi.fn()) {
  return mount(CollectionSearchInput, {
    props: { collectionCount: 1 },
    global: {
      mocks: { $store: { dispatch, getters: {} } },
      stubs: { VTextField: { template: '<input />', inheritAttrs: false } },
    },
  })
}

describe('collection search cleanup', () => {
  afterEach(() => vi.useRealTimers())

  it('cancels pending searches when leaving the collection without throwing', async () => {
    vi.useFakeTimers()
    const dispatch = vi.fn()
    const wrapper = mountSearch(dispatch)
    await wrapper.setData({ query: 'pending search' })
    expect(() => wrapper.unmount()).not.toThrow()
    await vi.runAllTimersAsync()
    expect(dispatch).not.toHaveBeenCalled()
  })

  it('keeps each mounted search field debounce independent', async () => {
    vi.useFakeTimers()
    const firstDispatch = vi.fn()
    const secondDispatch = vi.fn()
    const first = mountSearch(firstDispatch)
    const second = mountSearch(secondDispatch)
    await first.setData({ query: 'first query' })
    await second.setData({ query: 'second query' })
    await vi.advanceTimersByTimeAsync(100)
    expect(firstDispatch).toHaveBeenCalledWith('collection/setSearchQuery', 'first query')
    expect(secondDispatch).toHaveBeenCalledWith('collection/setSearchQuery', 'second query')
    first.unmount()
    second.unmount()
  })
})
