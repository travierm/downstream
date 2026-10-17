import { expect, test } from '@playwright/test'

const user = { id: 1, hash: 'listener', display_name: 'Listener', type: 'admin' }
const otherUser = { id: 2, hash: 'collector', display_name: 'Collector', media_count: 100, followers_count: 5 }
const song = {
  guid: 'saved-song', media_id: 1, index: 'video-one', title: 'Saved migration song',
  collected: true, thumbnail: 'https://images.example.test/song.svg',
}
const result = { guid: 'search-song', mediaId: 2, videoId: 'video-two', title: 'Search migration song', collected: false }

async function mockApp(page, { signedIn = true } = {}) {
  const errors = []
  page.on('pageerror', error => errors.push(error.message))
  // Production Vue does not emit development warnings; surface Vuetify warnings too.
  page.on('console', message => {
    if (message.type() === 'warn' && /Vue|Vuetify|VUE_ROUTER/.test(message.text())) errors.push(message.text())
  })
  const requests = []
  await page.addInitScript(({ user, signedIn }) => {
    if (signedIn) {
      localStorage.setItem('user', JSON.stringify(user))
      localStorage.setItem('token', 'test-token')
    }
  }, { user, signedIn })
  await page.route('**/*', async route => {
    const url = new URL(route.request().url())
    if (['127.0.0.1', 'localhost'].includes(url.hostname) && !url.pathname.startsWith('/api/')) return route.continue()
    if (url.hostname === 'images.example.test') return route.fulfill({ contentType: 'image/svg+xml', body: '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="250"><rect width="400" height="250" fill="purple"/></svg>' })
    if (url.hostname === 'suggestqueries.google.com') {
      return route.fulfill({ contentType: 'application/javascript', body: `${url.searchParams.get('callback')}(["", [["migration song"]]])` })
    }
    if (url.hostname !== 'api.downstream.us' && !(['127.0.0.1', 'localhost'].includes(url.hostname) && url.pathname.startsWith('/api/'))) return route.abort()
    const path = url.pathname.replace(/^\/api/, '')
    requests.push({ path, method: route.request().method(), body: route.request().postDataJSON() })
    let json = {}
    if (path === '/auth/login') json = { token: 'test-token', user }
    if (path === '/auth/user') json = user
    if (path.startsWith('/collection')) json = { hash: 'test-hash', items: [song], user }
    if (path === '/followage') json = { following: [], followers: [] }
    if (path === '/users/active') json = [user, otherUser]
    if (path === '/follow/2') json = { followedUser: otherUser }
    if (path === '/following/activity') json = { items: [] }
    if (path === '/spotify/stats') json = { data: [], categories: [] }
    if (path === '/ana/media/stats') json = { plays: { year: 10 } }
    if (path.startsWith('/search/')) json = { results: [result] }
    if (path === '/daily-mix') json = { videos: [{ ...result, title: 'Daily migration song' }] }
    if (path.startsWith('/discover/track/')) json = { items: [result] }
    if (path.startsWith('/video/')) json = { item: { title: song.title } }
    if (path === '/playlist/all') json = { items: [{ id: 42, name: 'Migration playlist', count: 1, itemAdded: false }] }
    if (path === '/user/stats') json = {
      play_count_history: { data: [1, 3], categories: ['Jan', 'Feb'] },
      collection_count_history: { data: [2, 4], categories: ['Jan', 'Feb'] },
    }
    if (path === '/admin/dashboard') json = {
      stats: { users: 42, new_users_week: 2, collections: 100, collections_week: 5, unique_songs: 80, plays_week: 20, active_collectors_week: 3 },
      activity: [{ date: '2026-10-16', users: 0, collections: 0, plays: 0 }, { date: '2026-10-17', users: 2, collections: 5, plays: 20 }],
      recent_users: [user], updated_at: '2026-10-17T12:00:00Z',
    }
    return route.fulfill({ json })
  })
  return { errors, requests }
}

test('navbar keeps its grey contrast against the page background', async ({ page }) => {
  await mockApp(page)
  await page.goto('/collection')
  await expect(page.locator('.v-app-bar').first()).toHaveCSS('background-color', 'rgb(39, 39, 39)')
  await expect(page.locator('.v-application')).toHaveCSS('background-color', 'rgb(18, 18, 18)')
})

test('player cards retain the original responsive widths across views', async ({ page }) => {
  await mockApp(page)
  for (const path of ['/collection', '/search?query=migration', '/daily-mix', '/discover/track/video-one', '/']) {
    await page.goto(path)
    const card = page.locator('.youtubeCardThumbnail').first()
    await expect(card).toBeVisible()
    for (const [width, columns] of [[390, 1], [599, 1], [600, 2], [959, 2], [960, 2], [1200, 2], [1263, 2], [1264, 4], [1545, 4], [1903, 4], [1904, 6], [2560, 6]]) {
      await page.setViewportSize({ width, height: 900 })
      // The original fluid grid left 24px between cards and 12px at each edge.
      await expect.poll(async () => (await card.boundingBox()).width,
        { message: `${path} at ${width}px should have ${columns} cards per row` },
      ).toBeCloseTo(width / columns - 24, 0)
    }
  }
})

test('bottom player controls have consistent sizing and alignment', async ({ page }) => {
  await mockApp(page)
  await page.goto('/collection')
  for (const width of [360, 390, 600, 960, 1440, 1920]) {
    await page.setViewportSize({ width, height: 900 })
    const bar = page.locator('.player-bar')
    await expect(bar).toHaveCSS('height', width >= 960 ? '48px' : '96px')
    await expect(bar.locator('.player-focus')).toHaveCSS('text-transform', 'uppercase')
    await expect(bar.locator('.player-focus')).toHaveCSS('min-width', '112px')
    await expect(bar.getByRole('button', { name: 'Replay current track' }).locator('.v-icon')).toHaveCSS('font-size', '32px')
    const buttons = bar.locator('.v-btn')
    await expect(buttons).toHaveCount(4)
    const bounds = await buttons.evaluateAll(elements => elements.map(element => {
      const { x, y, width, height } = element.getBoundingClientRect()
      return { x, y, width, height }
    }))
    expect(bounds.map(button => button.height), `button heights at ${width}px`).toEqual([36, 36, 36, 36])
    for (const button of bounds) {
      expect(Math.abs(button.y - bounds[0].y), `button alignment at ${width}px`).toBeLessThan(1)
      expect(button.x).toBeGreaterThanOrEqual(0)
      expect(button.x + button.width).toBeLessThanOrEqual(width)
    }
    expect(bounds[2].width).toBe(bounds[1].width)
    expect(bounds[2].x - bounds[1].x - bounds[1].width).toBe(8)
    expect(bounds[3].x - bounds[2].x - bounds[2].width).toBe(8)
    if (width >= 600) {
      await expect(bar.locator('.v-slider-track__fill')).toHaveCSS('background-color', 'rgb(124, 77, 255)')
      await expect(bar.locator('.v-slider-thumb__surface')).toHaveCSS('width', '12px')
      const track = await bar.locator('.v-slider-track').boundingBox()
      expect(track.width, `volume track width at ${width}px`).toBeGreaterThanOrEqual(60)
      expect(Math.abs(track.y + track.height / 2 - bounds[0].y - 18), `volume alignment at ${width}px`).toBeLessThan(1)
    }
  }
})

test('login waits for Vuetify validation and navigates after signing in', async ({ page }) => {
  const { errors, requests } = await mockApp(page, { signedIn: false })
  await page.goto('/login')
  await page.getByRole('button', { name: 'Login', exact: true }).click()
  await expect(page.getByText('Email is required')).toBeVisible()
  expect(requests.some(request => request.path === '/auth/login')).toBe(false)
  await page.getByLabel('Email', { exact: true }).fill('listener@example.test')
  await page.getByLabel('Password', { exact: true }).fill('test-password')
  await page.getByLabel('Password', { exact: true }).press('Enter')
  await expect(page).toHaveURL(/\/collection$/)
  await expect(page.getByText(song.title, { exact: true })).toBeVisible()
  expect(requests.filter(request => request.path === '/auth/login')).toHaveLength(1)
  expect(errors).toEqual([])
})

test('collection filters, clears, opens tooltips, and updates playback state', async ({ page }) => {
  const { errors } = await mockApp(page)
  await page.goto('/collection')
  await expect(page.getByText(song.title, { exact: true })).toBeVisible()
  const filter = page.getByRole('textbox', { name: 'Search 1 items in your collection', exact: true })
  await filter.fill('unmatched')
  await expect(page.getByText('No results found in your collection!')).toBeVisible()
  await page.getByRole('button', { name: /Clear Search 1 items/ }).click()
  await expect(page.getByText(song.title, { exact: true })).toBeVisible()
  await page.locator('.youtube-card .v-card-actions button').first().hover()
  await expect(page.getByRole('tooltip', { name: 'Restart the video' })).toBeVisible()
  // Keep YouTube offline; exercise the shared manager's updates from its real public API.
  await page.evaluate(() => {
    window.$manager.loadVideo = () => {}
    window.$manager.playGuid('saved-song')
  })
  await expect(page.locator('.youtube-card-glow--active')).toHaveCount(1)
  await page.locator('.v-app-bar').last().locator('button').first().click()
  await expect.poll(() => page.evaluate(() => window.$manager.guidQueue.length)).toBe(1)
  expect(errors).toEqual([])
})

test('search supports typed queries and autocomplete selection', async ({ page }) => {
  const { errors, requests } = await mockApp(page)
  await page.goto('/collection')
  const input = page.getByPlaceholder('Search for music...')
  await input.fill('migration')
  await expect(page.getByRole('option', { name: 'migration song' })).toBeVisible()
  await page.getByRole('option', { name: 'migration song' }).click()
  await expect(page).toHaveURL(/\/search\?query=migration\+song/)
  await expect(page.getByText(result.title, { exact: true })).toBeVisible()
  await page.locator('.collectBtn').click()
  await expect.poll(() => requests.some(request => request.path === '/media/collect')).toBe(true)
  expect(errors).toEqual([])
})

test('mobile navigation opens, closes outside, navigates, and logs out', async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 })
  const { errors } = await mockApp(page)
  await page.goto('/collection')
  const focusButton = page.getByRole('button', { name: 'Focus', exact: true })
  const focusBounds = await focusButton.boundingBox()
  expect(focusBounds.y + focusBounds.height).toBeLessThanOrEqual(844)
  const menu = page.getByRole('button', { name: 'Open navigation menu' })
  await menu.click()
  await expect(page.getByText('Daily Mix', { exact: true })).toBeVisible()
  await page.locator('.v-navigation-drawer__scrim').click({ position: { x: 5, y: 200 } })
  await expect(page.locator('.ds-nav-drawer')).not.toHaveClass(/v-navigation-drawer--active/)
  await menu.click()
  await page.getByText('Daily Mix', { exact: true }).click()
  await expect(page).toHaveURL(/\/daily-mix$/)
  await expect(page.getByText('Daily migration song', { exact: true })).toBeVisible()
  await menu.click()
  await page.getByRole('link', { name: 'Logout', exact: true }).click()
  await expect(page).toHaveURL('http://127.0.0.1:4173/')
  expect(await page.evaluate(() => localStorage.getItem('token'))).toBeNull()
  expect(errors).toEqual([])
})

test('admin and account charts render after lazy loading', async ({ page }) => {
  const { errors } = await mockApp(page)
  await page.goto('/admin')
  await expect(page.getByText('Total users', { exact: true })).toBeVisible()
  await expect(page.locator('.apexcharts-svg')).toHaveCount(2)
  await expect(page.locator('tbody')).toContainText('Listener')
  await page.goto('/stats')
  await expect(page.locator('.apexcharts-svg')).toHaveCount(2)
  await expect(page.locator('.apexcharts-xaxis').first()).toContainText('Jan')
  await expect(page.locator('.apexcharts-xaxis').last()).toBeEmpty()
  expect(errors).toEqual([])
})

test('every sidebar destination renders when navigating without reloading', async ({ page }) => {
  const { errors } = await mockApp(page)
  await page.goto('/collection')
  for (const [name, path, text] of [
    ['Admin Panel', '/admin', 'Total users'],
    ['Daily Mix', '/daily-mix', 'Daily migration song'],
    ['Stats', '/stats', 'Most Played Tracks'],
    ['Spotify Sync', '/spotify', 'Your Spotify Account is not connected'],
    ['Top Users', '/users', 'Top Users'],
    ['Follower Feed', '/follower/activity', 'Follower Feed'],
    ['Invite Friends', '/invite', 'Anyone can join Downstream'],
    ['Collection', '/collection', song.title],
  ]) {
    await page.getByRole('button', { name: 'Open navigation menu' }).click()
    await page.locator('.ds-nav-drawer-item').filter({ hasText: name }).click()
    await expect(page).toHaveURL(new URL(path, page.url()).href)
    await expect(page.locator('main')).toContainText(text)
    await expect(page.locator('.ds-nav-drawer')).not.toHaveClass(/v-navigation-drawer--active/)
  }
  expect(errors).toEqual([])
})

test('user cards follow and unfollow users and recover from failed requests', async ({ page }) => {
  const { errors, requests } = await mockApp(page)
  await page.goto('/users')
  const card = page.locator('.user-card').filter({ hasText: otherUser.display_name })
  await expect(card).toBeVisible()
  await expect(page.getByText('The Downstream community', { exact: true })).toHaveCount(0)
  await expect(page.locator('.user-card').filter({ hasText: 'Listener' }).getByRole('button', { name: /follow/i })).toHaveCount(0)
  const followers = card.locator('.profile-stats > div').last().locator('dd')
  await card.getByRole('button', { name: 'Follow Collector', exact: true }).click()
  await expect(card.getByRole('button', { name: 'Unfollow Collector', exact: true })).toBeVisible()
  await expect(followers).toHaveText('6')
  await card.getByRole('button', { name: 'Unfollow Collector', exact: true }).click()
  await expect(card.getByRole('button', { name: 'Follow Collector', exact: true })).toBeVisible()
  await expect(followers).toHaveText('5')
  expect(requests.some(request => request.path === '/follow/2' && request.method === 'PUT')).toBe(true)
  expect(requests.some(request => request.path === '/unfollow/2' && request.method === 'DELETE')).toBe(true)
  await page.route('**/api/follow/2', route => route.fulfill({ status: 500, json: {} }))
  await card.getByRole('button', { name: 'Follow Collector', exact: true }).click()
  await expect(card.getByRole('alert')).toContainText('Couldn’t update follow status')
  await expect(followers).toHaveText('5')
  await page.unroute('**/api/follow/2')
  await card.getByRole('button', { name: 'Follow Collector', exact: true }).click()
  await expect(card.getByRole('alert')).toHaveCount(0)
  await expect(followers).toHaveText('6')
  await page.setViewportSize({ width: 390, height: 844 })
  await expect(card.getByRole('link', { name: "View Collector's profile" })).toHaveAttribute('href', '/profile/collector')
  await expect(card.getByRole('button', { name: 'Unfollow Collector', exact: true })).toBeVisible()
  expect(errors).toEqual([])
})

test('playlist drawer retains selection and navigation', async ({ page }) => {
  const { errors, requests } = await mockApp(page)
  await page.goto('/collection')
  // The playlist trigger is currently hidden in the UI; open the existing drawer through Vuex.
  await page.evaluate(async () => {
    const app = document.querySelector('#app').__vue_app__
    await app.config.globalProperties.$store.dispatch('playlist/getAll')
    app.config.globalProperties.$store.dispatch('setPlaylistDrawerStatus', true)
  })
  await page.getByText('Migration playlist', { exact: true }).click()
  await expect(page).toHaveURL(/playlist_id=42/)
  await expect.poll(() => requests.filter(request => request.path === '/collection').length).toBeGreaterThan(1)
  await expect(page.getByText(song.title, { exact: true })).toBeVisible()
  expect(errors).toEqual([])
})

test('community cards show profile stats and fit mobile and desktop layouts', async ({ page }) => {
  const { errors } = await mockApp(page)
  await page.route('**/users/active', route => route.fulfill({ json: Array.from({ length: 9 }, (_, index) => ({
    id: index + 1, hash: `collector-${index}`, display_name: index === 0 ? 'A collector with a particularly long display name' : `Collector ${index}`,
    created_at: '2021-06-15T00:00:00.000000Z', media_count: 12345,
    spotify_imports_count: index === 0 ? 1200 : 0, followers_count: 42,
  })) }))
  await page.goto('/users')
  await expect(page.getByRole('heading', { name: 'Top Users', exact: true })).toBeVisible()
  const cards = page.locator('.user-card')
  await expect(cards).toHaveCount(9)
  await expect(cards.first()).toContainText('June 15, 2021')
  await expect(cards.first()).toContainText('12,345')
  await expect(cards.first()).toContainText('1,200')
  await expect(cards.first().locator('.collection-stat .text-headline-large')).toHaveCSS('font-size', '32px')
  await expect(cards.first().locator('.collection-stat svg path')).toHaveAttribute('d', /^M/)
  await expect(cards.nth(1).locator('dd').first()).toHaveText('0')
  await expect(cards.first().getByRole('link')).toHaveAttribute('href', '/profile/collector-0')
  for (const [width, columns] of [[360, 1], [768, 2], [1440, 3]]) {
    await page.setViewportSize({ width, height: 900 })
    const first = await cards.first().boundingBox()
    const nextRow = await cards.nth(columns).boundingBox()
    expect(nextRow.y).toBeGreaterThan(first.y)
    if (columns > 1) {
      const neighbor = await cards.nth(1).boundingBox()
      expect(neighbor.y).toBe(first.y)
      expect(neighbor.height).toBe(first.height)
    }
    expect(await page.evaluate(() => document.documentElement.scrollWidth)).toBeLessThanOrEqual(width)
    await expect(cards.first()).toHaveCSS('border-radius', '24px')
  }
  expect(errors).toEqual([])
})

test('community request failures can be retried and empty results are explained', async ({ page }) => {
  await mockApp(page)
  let fail = true
  await page.route('**/users/active', route => fail
    ? route.fulfill({ status: 503, json: {} })
    : route.fulfill({ json: [] }))
  await page.goto('/users')
  await expect(page.locator('.users-view').getByRole('alert')).toContainText('We couldn’t load the community')
  fail = false
  await page.getByRole('button', { name: 'Try again' }).click()
  await expect(page.getByRole('heading', { name: 'A community in the making' })).toBeVisible()
})
