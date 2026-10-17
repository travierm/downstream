<template>
  <v-container class="d-flex flex-wrap justify-center px-0 px-sm-3">
    <v-sheet elevation="2" class="px-2 px-sm-10 rounded" width="100%" max-width="560">
      <v-row class="mx-0">
        <v-col class="px-0">
          <h2 class="mt-4 mb-4">Create an account on Downstream</h2>
          <h3>We're excited to have you join us!</h3>

          <alert-list style="margin-bottom: 0px" class="mt-2" ref="alertList"></alert-list>
        </v-col>
      </v-row>

      <v-row class="mb-2 mx-0">
        <v-col class="px-0">
          <v-form ref="registerForm" v-model="valid" @submit.prevent="register">
            <v-text-field name="email" type="email" autocomplete="email" variant="outlined" v-model="email" label="Email"
              :rules="[(v) => !!v || 'Email is required']" required></v-text-field>

            <v-text-field name="username" autocomplete="nickname" variant="outlined" v-model="display_name" label="Display Name"
              :rules="[(v) => !!v || 'Display Name is required']" required></v-text-field>

            <v-text-field name="password" autocomplete="new-password" variant="outlined" v-model="password" type="password" label="Password" :rules="[
              (v) => !!v || 'Password is required',
              (v) =>
                (v || '').length >= 6 || 'Password must be 6 or more characters long',
            ]" required></v-text-field>

            <v-text-field name="password_confirmation" autocomplete="new-password" variant="outlined" v-model="password_confirmation" type="password"
              label="Confirm Password" :rules="[
                (v) => v === this.password || 'Both passwords must match',
              ]" required></v-text-field>

            <turnstile-challenge ref="turnstile" v-model="turnstileToken" />
            <v-btn color="primary" class="loginBtn" type="submit" :loading="submitting"
              :disabled="!turnstileToken || submitting">Register</v-btn>
            <v-btn variant="text" class="ml-2" to="/login">Log in</v-btn>
          </v-form>
        </v-col>
      </v-row>

      <p class="mt-5 text-caption">By registering, you agree to our <router-link to="/tos">Terms of
          Service</router-link>
        and <router-link to="/privacy">Privacy Policy</router-link>.</p>
    </v-sheet>
  </v-container>
</template>

<script>
import TurnstileChallenge from '@/components/Shared/TurnstileChallenge.vue'
import { registerUser } from '../../../services/api/UserRegistrationService'

export default {
  name: 'RegisterView',
  components: { TurnstileChallenge },
  data: function () {
    return {
      valid: false,
      turnstileToken: '',
      submitting: false,
      email: '',
      display_name: '',
      password: '',
      password_confirmation: '',
    }
  },
  methods: {
    async register() {
      if (this.submitting || !this.turnstileToken) return
      this.submitting = true
      let registrationAttempted = false
      try {
        const { valid } = await this.$refs.registerForm.validate()
        if (!valid) return
        registrationAttempted = true
        const response = await registerUser(
          this.display_name,
          this.email,
          this.password,
          this.password_confirmation,
          this.turnstileToken
        )
        this.$refs.alertList.create('success', response.data.message)
        await this.$store.dispatch('auth/login', { email: this.email, password: this.password })
        if (this.$store.getters['auth/loggedIn']) {
          await this.$router.push('/collection')
        } else {
          await this.$router.push('/login')
        }
      } catch (response) {
        const errors = response?.data?.errors
        const message = errors ? Object.values(errors).flat().join(' ') : response?.data?.message
        this.$refs.alertList.create('error', message || 'Registration failed. Please try again.')
      } finally {
        this.submitting = false
        if (registrationAttempted) this.$refs.turnstile?.reset()
      }
    },
  },
}
</script>
