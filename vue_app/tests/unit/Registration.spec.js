import { shallowMount } from '@vue/test-utils'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import RegisterView from '../../src/components/views/Auth/RegisterView.vue'
import TurnstileChallenge from '../../src/components/Shared/TurnstileChallenge.vue'
import { registerUser } from '../../src/services/api/UserRegistrationService'

vi.mock('../../src/services/api/UserRegistrationService', () => ({ registerUser: vi.fn() }))

const flush = () => new Promise(resolve => setTimeout(resolve, 0))

describe('registration', () => {
  let wrapper, store, router, validate, alert, reset
  beforeEach(() => {
    vi.clearAllMocks()
    validate = vi.fn().mockResolvedValue({ valid: true })
    alert = vi.fn()
    reset = vi.fn()
    store = { dispatch: vi.fn().mockResolvedValue(), getters: { 'auth/loggedIn': true } }
    router = { push: vi.fn().mockResolvedValue() }
    wrapper = shallowMount(RegisterView, { global: {
      mocks: { $vuetify: { display: { smAndUp: true } }, $store: store, $router: router },
      stubs: {
        'v-form': { template: '<form><slot /></form>', methods: { validate } },
        AlertList: { template: '<div />', methods: { create: alert } },
        TurnstileChallenge: { template: '<div />', methods: { reset } },
        'router-link': true,
      },
    } })
    wrapper.setData({ email: 'new@example.com', display_name: 'Listener', password: 'secret123',
      password_confirmation: 'secret123', turnstileToken: 'verified-token' })
  })

  it('requires both valid fields and a verification token', async () => {
    await wrapper.setData({ turnstileToken: '' })
    await wrapper.vm.register()
    expect(registerUser).not.toHaveBeenCalled()
    await wrapper.setData({ turnstileToken: 'verified-token' })
    validate.mockResolvedValue({ valid: false })
    await wrapper.vm.register()
    expect(registerUser).not.toHaveBeenCalled()
    expect(reset).not.toHaveBeenCalled()
    wrapper.unmount()
  })

  it('sends confirmation and token, logs in, and opens the collection', async () => {
    registerUser.mockResolvedValue({ data: { message: 'Created' } })
    await wrapper.vm.register()
    expect(registerUser).toHaveBeenCalledWith('Listener', 'new@example.com', 'secret123', 'secret123', 'verified-token')
    expect(store.dispatch).toHaveBeenCalledWith('auth/login', { email: 'new@example.com', password: 'secret123' })
    expect(router.push).toHaveBeenCalledWith('/collection')
    expect(reset).toHaveBeenCalledOnce()
    wrapper.unmount()
  })

  it('shows API validation errors and refreshes the single-use token', async () => {
    registerUser.mockRejectedValue({ data: { errors: { email: ['Email already registered.'] } } })
    await wrapper.vm.register()
    expect(alert).toHaveBeenCalledWith('error', 'Email already registered.')
    expect(reset).toHaveBeenCalledOnce()
    expect(wrapper.vm.submitting).toBe(false)
    expect(store.dispatch).not.toHaveBeenCalled()
    wrapper.unmount()
  })

  it('blocks duplicate submissions while registration is pending', async () => {
    let resolve
    registerUser.mockImplementation(() => new Promise(done => { resolve = done }))
    const pending = wrapper.vm.register()
    await wrapper.vm.register()
    await flush()
    expect(registerUser).toHaveBeenCalledOnce()
    resolve({ data: { message: 'Created' } })
    await pending
    wrapper.unmount()
  })

  it('sends a created account to login if automatic login fails', async () => {
    registerUser.mockResolvedValue({ data: { message: 'Created' } })
    store.getters['auth/loggedIn'] = false
    await wrapper.vm.register()
    expect(router.push).toHaveBeenCalledWith('/login')
    wrapper.unmount()
  })
})

describe('Turnstile challenge', () => {
  beforeEach(() => {
    window.turnstile = { render: vi.fn(() => 'widget-id'), reset: vi.fn(), remove: vi.fn() }
  })

  const mountChallenge = () => shallowMount(TurnstileChallenge, {
    data: () => ({ siteKey: 'test-site-key' }),
  })

  it('passes the site key and registration action and clears expired or failed tokens', async () => {
    const wrapper = mountChallenge()
    await flush()
    const options = window.turnstile.render.mock.calls[0][1]
    expect(options.sitekey).toBe('test-site-key')
    expect(options.action).toBe('register')
    options.callback('token')
    options['expired-callback']()
    options['timeout-callback']()
    options['error-callback']()
    expect(wrapper.emitted('update:modelValue')).toEqual([['token'], [''], [''], ['']])
    expect(wrapper.vm.error).toContain('retry')
    wrapper.vm.retry()
    expect(window.turnstile.reset).toHaveBeenCalledWith('widget-id')
    wrapper.unmount()
    expect(window.turnstile.remove).toHaveBeenCalledWith('widget-id')
  })

  it('blocks registration when the site key is missing', async () => {
    const wrapper = shallowMount(TurnstileChallenge, { data: () => ({ siteKey: '' }) })
    await flush()
    expect(window.turnstile.render).not.toHaveBeenCalled()
    expect(wrapper.vm.error).toContain('unavailable')
    wrapper.unmount()
  })

  it('allows retry after the Cloudflare script fails to load', async () => {
    delete window.turnstile
    const wrapper = mountChallenge()
    const script = document.querySelector('script[src*="challenges.cloudflare.com"]')
    script.dispatchEvent(new Event('error'))
    await flush()
    expect(wrapper.vm.error).toContain('retry')
    wrapper.vm.retry()
    const retriedScript = document.querySelector('script[src*="challenges.cloudflare.com"]')
    window.turnstile = { render: vi.fn(() => 'retried-widget'), remove: vi.fn() }
    retriedScript.dispatchEvent(new Event('load'))
    await flush()
    expect(window.turnstile.render).toHaveBeenCalledOnce()
    wrapper.unmount()
    retriedScript.remove()
  })
})
