import { beforeEach, describe, expect, it, vi } from 'vitest'
import { actions, mutations } from '../../src/store/modules/discover'
import DiscoverService from '../../src/services/api/DiscoverService'

vi.mock('../../src/services/api/DiscoverService', () => ({ default: { getSimilarTracksByVideoId: vi.fn() } }))

function context() {
  const ctx = {
    state: { similarTracks: {}, errorMessage: false },
    rootState: { auth: { user: { id: 1 }, token: 'token' } },
    dispatch: vi.fn().mockResolvedValue(),
  }
  ctx.commit = vi.fn((name, value) => mutations[name](ctx.state, value))
  return ctx
}
const items = [{ videoId: 'a', guid: 'guid_a' }]

describe('Discover requests', () => {
  beforeEach(() => vi.clearAllMocks())

  it('shares pending requests and reuses recent results while updating the player', async () => {
    let complete
    DiscoverService.getSimilarTracksByVideoId.mockReturnValue(new Promise(resolve => { complete = resolve }))
    const ctx = context()
    const first = actions.getSimilarTracks(ctx, 'a')
    expect(actions.getSimilarTracks(ctx, 'a')).toBe(first)
    complete({ data: { items } })
    await first
    await actions.getSimilarTracks(ctx, 'a')
    expect(DiscoverService.getSimilarTracksByVideoId).toHaveBeenCalledTimes(1)
    expect(ctx.dispatch.mock.calls.filter(([name]) => name === 'player/updateGuidData')).toEqual([
      ['player/updateGuidData', { guidIndexKey: '/discover/track/a', mediaItems: items }, { root: true }],
      ['player/updateGuidData', { guidIndexKey: '/discover/track/a', mediaItems: items }, { root: true }],
    ])
  })

  it('refreshes expired data and isolates signed-in sessions', async () => {
    const now = vi.spyOn(Date, 'now').mockReturnValue(1000)
    DiscoverService.getSimilarTracksByVideoId.mockResolvedValue({ data: { items } })
    const ctx = context()
    await actions.getSimilarTracks(ctx, 'a')
    now.mockReturnValue(62000)
    await actions.getSimilarTracks(ctx, 'a')
    ctx.rootState.auth.user = { id: 2 }
    await actions.getSimilarTracks(ctx, 'a')
    expect(DiscoverService.getSimilarTracksByVideoId).toHaveBeenCalledTimes(3)
    now.mockRestore()
  })

  it('clears loading and allows retry after network or provider errors', async () => {
    DiscoverService.getSimilarTracksByVideoId.mockRejectedValueOnce({ status: 503, data: { message: 'Try again' } })
      .mockRejectedValueOnce(undefined).mockResolvedValue({ data: { items } })
    const ctx = context()
    await actions.getSimilarTracks(ctx, 'a')
    expect(ctx.state.errorMessage).toBe('Try again')
    await actions.getSimilarTracks(ctx, 'a')
    expect(ctx.state.errorMessage).toContain('Could not load')
    await actions.getSimilarTracks(ctx, 'a')
    expect(ctx.state.errorMessage).toBe(false)
    expect(ctx.dispatch).toHaveBeenLastCalledWith('setLoadingBarState', false, { root: true })
  })

  it('ignores results arriving after the signed-in session changes', async () => {
    let finishOld
    DiscoverService.getSimilarTracksByVideoId.mockReturnValueOnce(new Promise(resolve => { finishOld = resolve }))
      .mockResolvedValueOnce({ data: { items: [{ videoId: 'new', guid: 'guid_new' }] } })
    const ctx = context()
    const old = actions.getSimilarTracks(ctx, 'a')
    ctx.rootState.auth.user = { id: 2 }
    await actions.getSimilarTracks(ctx, 'a')
    finishOld({ data: { items } })
    await old
    expect(ctx.state.similarTracks.a[0].videoId).toBe('new')
    expect(ctx.dispatch.mock.calls.filter(([name]) => name === 'player/updateGuidData')).toHaveLength(1)
  })

  it('keeps loading active until all distinct pending requests finish', async () => {
    let finishA, finishB
    DiscoverService.getSimilarTracksByVideoId.mockReturnValueOnce(new Promise(resolve => { finishA = resolve }))
      .mockReturnValueOnce(new Promise(resolve => { finishB = resolve }))
    const ctx = context()
    const a = actions.getSimilarTracks(ctx, 'a')
    const b = actions.getSimilarTracks(ctx, 'b')
    finishA({ data: { items } })
    await a
    expect(ctx.dispatch).not.toHaveBeenCalledWith('setLoadingBarState', false, { root: true })
    finishB({ data: { items } })
    await b
    expect(ctx.dispatch).toHaveBeenLastCalledWith('setLoadingBarState', false, { root: true })
  })
})
