<template>
  <div class="mb-4">
    <div ref="widget"></div>
    <v-alert v-if="error" type="error" role="alert">{{ error }}</v-alert>
    <v-btn v-if="error && siteKey" small text @click="retry">Retry verification</v-btn>
  </div>
</template>

<script>
let scriptPromise

function loadTurnstile() {
  if (window.turnstile) return Promise.resolve(window.turnstile)
  if (!scriptPromise) {
    scriptPromise = new Promise((resolve, reject) => {
      const script = document.createElement('script')
      script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit'
      script.async = true
      script.onload = () => resolve(window.turnstile)
      script.onerror = () => {
        script.remove()
        scriptPromise = null
        reject(new Error('Unable to load verification'))
      }
      document.head.appendChild(script)
    })
  }
  return scriptPromise
}

export default {
  name: 'TurnstileChallenge',
  data: () => ({
    siteKey: import.meta.env.VITE_TURNSTILE_SITE_KEY,
    widgetId: null,
    error: '',
  }),
  mounted() {
    this.renderWidget()
  },
  beforeDestroy() {
    if (this.widgetId !== null && window.turnstile) {
      window.turnstile.remove(this.widgetId)
    }
  },
  methods: {
    async renderWidget() {
      if (!this.siteKey) {
        this.error = 'Registration verification is unavailable. Please try again later.'
        return
      }
      try {
        const turnstile = await loadTurnstile()
        if (this._isDestroyed || this._isBeingDestroyed) return
        this.widgetId = turnstile.render(this.$refs.widget, {
          sitekey: this.siteKey,
          action: 'register',
          size: 'compact',
          callback: token => {
            this.error = ''
            this.$emit('input', token)
          },
          'expired-callback': () => this.$emit('input', ''),
          'error-callback': () => {
            this.$emit('input', '')
            this.error = 'Verification failed to load. Please retry.'
          },
          'timeout-callback': () => this.$emit('input', ''),
        })
      } catch (error) {
        if (!this._isDestroyed) {
          this.error = 'Verification failed to load. Please retry.'
        }
      }
    },
    reset() {
      this.$emit('input', '')
      if (this.widgetId !== null && window.turnstile) {
        window.turnstile.reset(this.widgetId)
      }
    },
    retry() {
      this.error = ''
      if (this.widgetId !== null) this.reset()
      else this.renderWidget()
    },
  },
}
</script>
