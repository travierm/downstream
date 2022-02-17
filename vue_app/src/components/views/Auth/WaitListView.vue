<template>
  <v-container class="d-flex flex-wrap justify-center" @keyup.enter="login()">
    <!-- Login Sheet -->
    <v-sheet
      elevation="2"
      class="pl-10 pr-10"
      :width="sheetWidth"
      style="min-width: 250px"
    >
      <div>
        <!-- Login Header Text -->
        <v-row class="mt-2">
          <v-col>
            <h1>Wait List</h1>

            <div class="text-body-1">
              Downstream is not currently open to registration without an invite
              code.
            </div>
            <div class="text-body-1 mt-2">
              Please sign up on the wait list and will get you an account as
              soon as possible.
            </div>
          </v-col>
        </v-row>

        <!-- Login Form -->
        <v-row class="mb-2">
          <v-col>
            <v-form ref="loginForm" v-model="valid">
              <!-- Login Form Inputs -->
              <v-text-field
                name="email"
                outlined
                v-model="email"
                label="Email"
                :rules="[(v) => !!v || 'Email is required']"
                required
              ></v-text-field>

              <v-text-field
                name="password"
                outlined
                v-model="password"
                type="password"
                label="Password"
                :rules="[(v) => !!v || 'Password is required']"
                required
              ></v-text-field>
              <v-checkbox v-model="rememberMe" label="Remember Me"></v-checkbox>

              <!-- Login Form Actions -->
              <v-btn class="loginBtn" @click="login">Login</v-btn>
              <v-btn class="ml-2" small text color="primary"
                >Forgot Password?</v-btn
              >
            </v-form>
          </v-col>
        </v-row>
      </div>
    </v-sheet>
  </v-container>
</template>

<script>
import { mapState } from 'vuex'

const afterLoginRoute = '/collection'

export default {
  name: 'WaitListView',
  components: {},
  computed: {
    ...mapState('auth', ['error']),
    sheetWidth() {
      return this.$vuetify.breakpoint.smAndUp ? '40%' : '100%)'
    },
  },
  data: () => {
    return {
      email: '',
      password: '',
      rememberMe: false,
      valid: false,
      failedLogin: false,
    }
  },
  methods: {
    login() {
      if (!this.$refs.loginForm.validate()) {
        return
      }

      const params = {
        email: this.email,
        password: this.password,
      }
      this.$store.dispatch('auth/login', params).then(() => {
        if (!this.error) {
          this.$router.push(afterLoginRoute)
        }
      })
    },
  },
}
</script>
