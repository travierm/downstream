<template>
    <v-container class="d-flex flex-wrap justify-center" @keyup.enter="login()">
      <!-- Login Sheet -->
      <v-sheet elevation=2 class="pl-10 pr-10" width="40%" style="min-width: 250px;">
        <div>
          <!-- Login Header Text -->
          <v-row>
            <v-col>
                <h1>Login</h1>

                <v-alert v-if="error" type="error" class="mt-2 mb-0">
                  Email or Password is incorrect!
                </v-alert>
            </v-col>
          </v-row>

          <!-- Login Form -->
          <v-row>
            <v-col>
              <v-form ref="loginForm" v-model="valid">
                <!-- Login Form Inputs -->
                <v-text-field solo v-model="email" label="Email" :rules="[v => !!v || 'Email is required']" required></v-text-field>
                <v-text-field solo v-model="password" type="password" label="Password" :rules="[v => !!v || 'Password is required']" required></v-text-field>
                <v-checkbox v-model="rememberMe" label="Remember Me"></v-checkbox>

                <!-- Login Form Actions -->
                <v-btn @click="login">Login</v-btn>
                <v-btn class="ml-2" small text color="primary">Forgot Password?</v-btn>
              </v-form>
            </v-col>
          </v-row>
        </div>
      </v-sheet>
    </v-container>
</template>

<script>
import { mapState } from 'vuex';

const afterLoginRoute = '/collection'

export default {
  name: 'LoginPage',
  components: {},
  computed: {
    ...mapState('auth', ['error'])
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
    login () {
      if (!this.$refs.loginForm.validate()) {
        return;
      }

      const params = {
        email: this.email,
        password: this.password
      }
      this.$store.dispatch('auth/login', params)
        .then(() => {
          if(!this.error) {

            this.$router.push(afterLoginRoute)
          }
        })
    }
  }
}
</script>