import { config } from '@vue/test-utils'

config.global.renderStubDefaultSlot = true

// Use the browser environment's storage even when Node exposes its own Web Storage API.
for (const key of ['localStorage', 'sessionStorage']) {
  Object.defineProperty(globalThis, key, {
    configurable: true,
    value: globalThis.jsdom.window[key],
  })
}
