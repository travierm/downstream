<template>
  <v-container
    rounded
    class="d-flex flex-wrap justify-center"
    @keyup.enter="login()"
  >
    <!-- Login Sheet -->
    <v-sheet
      elevation="2"
      class="pl-10 pr-10 rounded"
      :width="sheetWidth"
      style="min-width: 250px"
    >
      <div>
        <!-- Login Header Text -->
        <v-row class="mt-2">
          <v-col>
            <h1>Login</h1>
            <p class="mt-2">An invite is required to join downstream.</p>

            <v-alert v-if="error" type="error" class="mt-2 mb-0">
              Email or Password is incorrect!
            </v-alert>
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

              <!-- <v-checkbox v-model="rememberMe" label="Remember Me"></v-checkbox> -->

              <!-- Login Form Actions -->
              <v-btn color="primary" class="loginBtn" @click="login"
                >Login</v-btn
              >
              <!--<v-btn color="primary" class="ml-2" to="/waitlist"
                >Join our waiting list</v-btn
              > -->
              <!-- <v-btn class="ml-2" small text color="primary"
                >Forgot Password?</v-btn
              > -->
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
  name: 'LoginView',
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
