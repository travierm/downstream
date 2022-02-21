<template>
  <v-container class="d-flex flex-wrap justify-center">
    <v-sheet elevation="2" class="pl-10 pr-10 rounded" :width="sheetWidth">
      <v-row>
        <v-col>
          <h1>Create an account on Downstream</h1>
          <h3>We're excited to have you join us!</h3>

          <alert-list
            style="margin-bottom: 0px"
            class="mt-2"
            ref="alertList"
          ></alert-list>
        </v-col>
      </v-row>

      <v-row class="mb-2">
        <v-col>
          <v-form ref="registerForm" v-model="valid">
            <v-text-field
              name="email"
              outlined
              v-model="invite_code"
              label="Invite Code"
              :rules="[(v) => !!v || 'Invite Code is required']"
              required
            ></v-text-field>

            <v-text-field
              name="email"
              outlined
              v-model="email"
              label="Email"
              :rules="[(v) => !!v || 'Email is required']"
              required
            ></v-text-field>

            <v-text-field
              name="username"
              outlined
              v-model="display_name"
              label="Display Name"
              :rules="[(v) => !!v || 'Display Name is required']"
              required
            ></v-text-field>

            <v-text-field
              name="password"
              outlined
              v-model="password"
              type="password"
              label="Password"
              :rules="[
                (v) => !!v || 'Password is required',
                (v) =>
                  v.length >= 6 || 'Password must be 6 or more characters long',
              ]"
              required
            ></v-text-field>

            <v-text-field
              name="password"
              outlined
              v-model="password_confirmation"
              type="password"
              label="Confirm Password"
              :rules="[
                (v) => v === this.password || 'Both password must match',
              ]"
              required
            ></v-text-field>

            <v-btn color="primary" class="loginBtn" @click="register"
              >Register</v-btn
            >
            <v-btn color="primary" class="ml-2" to="/waitlist"
              >Join our waiting list</v-btn
            >
          </v-form>
        </v-col>
      </v-row>
    </v-sheet>
  </v-container>
</template>

<script>
import { registerUser } from '../../../services/api/UserRegistrationService'

export default {
  name: 'RegisterView',
  computed: {
    sheetWidth() {
      return this.$vuetify.breakpoint.smAndUp ? '40%' : '100%)'
    },
  },
  data: function () {
    return {
      valid: false,
      invite_code: this.$route.query.invite ?? '',
      email: '',
      display_name: '',
      password: '',
      password_confirmation: '',
    }
  },
  mounted() {},
  methods: {
    register() {
      if (!this.$refs.registerForm.validate()) {
        return
      }

      registerUser(
        this.display_name,
        this.email,
        this.password,
        this.invite_code
      )
        .then((response) => {
          this.$refs.alertList.create('success', response.data.message)

          const params = {
            email: this.email,
            password: this.password,
          }
          this.$store.dispatch('auth/login', params).then(() => {
            this.$router.push('/collection')
          })
        })
        .catch((response) => {
          this.$refs.alertList.create('error', response.data.message)
        })
    },
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
