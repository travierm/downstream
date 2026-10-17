import { expect, test } from '@playwright/test'

const pages = [
  ['/', 'A private music collection service'],
  ['/login', 'An invite is required'],
  ['/register', 'Create an account on Downstream'],
  ['/privacy', 'Privacy Policy of Downstream'],
  ['/tos', 'TERMS OF SERVICE'],
]

for (const [path, content] of pages) {
  test(`production app starts at ${path}`, async ({ page }) => {
    const errors = []
    page.on('pageerror', error => errors.push(error.message))

    await page.route('**/*', route => {
      const { hostname } = new URL(route.request().url())
      if (hostname === '127.0.0.1') return route.continue()
      if (hostname === 'api.downstream.us') {
        return route.fulfill({ json: {} })
      }
      return route.abort()
    })

    const response = await page.goto(path)
    expect(response.status()).toBe(200)
    await expect(page.locator('#app')).toContainText(content)
    expect(errors).toEqual([])
  })
}
