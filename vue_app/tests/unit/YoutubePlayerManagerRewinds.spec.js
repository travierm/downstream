import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'

vi.mock('yt-player', () => ({
  default: class {
    constructor() { this.events = {} }
    on(event, callback) { this.events[event] = callback }
    setVolume() {}
    load() {}
    stop() {}
    destroy() {}
  },
}))
vi.mock('../../src/services/api/AnalyticsService', () => ({
  default: { playedMedia: vi.fn() },
}))

import manager from '../../src/services/YoutubePlayerManager'
import analytics from '../../src/services/api/AnalyticsService'

describe('rewind play counting', () => {
  beforeEach(() => {
    vi.useFakeTimers()
    vi.setSystemTime(new Date('2026-10-17T12:00:00Z'))
    vi.clearAllMocks()
    document.body.innerHTML = '<div id="downstream-video-container"></div>'
    manager.destroyPlayer()
    manager.updateGuidVideoMap({
      first: { id: 1, videoId: 'first-video' },
      second: { id: 2, videoId: 'second-video' },
    })
    manager.playGuid('first')
    analytics.playedMedia.mockClear()
  })

  afterEach(() => { vi.useRealTimers() })

  const tick = (seconds) => manager.videoPlayerInstance.events.timeupdate(seconds)

  it('ignores small seeks, including those near the start', () => {
    tick(10)
    tick(5)
    tick(100)
    tick(71)
    expect(analytics.playedMedia).not.toHaveBeenCalled()
  })

  it('counts a seek back of at least 30 seconds, even late in a track', () => {
    tick(180)
    tick(150)
    expect(analytics.playedMedia.mock.calls).toEqual([[1, 'rewind']])
  })

  it('counts at most one rewind every 30 seconds', () => {
    tick(180)
    tick(120)
    vi.advanceTimersByTime(29999)
    tick(60)
    expect(analytics.playedMedia).toHaveBeenCalledTimes(1)
    vi.advanceTimersByTime(1)
    tick(30)
    expect(analytics.playedMedia.mock.calls).toEqual([[1, 'rewind'], [1, 'rewind']])
  })

  it('resets rewind tracking when loading another track', () => {
    tick(180)
    tick(120)
    manager.playGuid('second')
    analytics.playedMedia.mockClear()
    tick(0)
    expect(analytics.playedMedia).not.toHaveBeenCalled()
    tick(60)
    tick(0)
    expect(analytics.playedMedia.mock.calls).toEqual([[2, 'rewind']])
  })

  it('resets rewind tracking when stopping playback', () => {
    tick(180)
    tick(120)
    manager.stopPlayingGuid()
    analytics.playedMedia.mockClear()
    tick(0)
    expect(analytics.playedMedia).not.toHaveBeenCalled()
    tick(60)
    tick(0)
    expect(analytics.playedMedia.mock.calls).toEqual([[1, 'rewind']])
  })
})
