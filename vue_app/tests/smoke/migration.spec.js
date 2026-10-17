import { expect, test } from '@playwright/test'

const user = { id: 1, hash: 'listener', display_name: 'Listener', type: 'admin' }
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
    if (message.type() === 'warn' && /Vue|Vuetify/.test(message.text())) errors.push(message.text())
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
    if (url.hostname === '127.0.0.1') return route.continue()
    if (url.hostname === 'images.example.test') return route.fulfill({ contentType: 'image/svg+xml', body: '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="250"><rect width="400" height="250" fill="purple"/></svg>' })
    if (url.hostname === 'suggestqueries.google.com') {
      return route.fulfill({ contentType: 'application/javascript', body: `${url.searchParams.get('callback')}(["", [["migration song"]]])` })
    }
    if (url.hostname !== 'api.downstream.us') return route.abort()
    const path = url.pathname.replace(/^\/api/, '')
    requests.push({ path, method: route.request().method(), body: route.request().postDataJSON() })
    let json = {}
    if (path === '/auth/login') json = { token: 'test-token', user }
    if (path === '/auth/user') json = user
    if (path.startsWith('/collection')) json = { hash: 'test-hash', items: [song], user }
    if (path === '/followage') json = { following: [], followers: [] }
    if (path === '/ana/media/stats') json = { plays: { year: 10 } }
    if (path.startsWith('/search/')) json = { results: [result] }
    if (path === '/daily-mix') json = { videos: [{ ...result, title: 'Daily migration song' }] }
    if (path.startsWith('/discover/track/')) json = { items: [result] }
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
