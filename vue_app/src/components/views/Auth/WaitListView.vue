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
            <h1 class="mb-2">Sign up for our Waiting List</h1>

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

        <!-- Waiting List Signup -->
        <v-row class="mb-2">
          <v-col align="center" justify="center">
            <v-form ref="waitListForm" v-model="valid">
              <v-text-field
                name="email"
                outlined
                v-model="email"
                label="Email"
                :rules="[(v) => !!v || 'Email is required']"
                required
              ></v-text-field>

              <v-textarea
                name="input-7-1"
                outlined
                v-model="textResponse"
                rows="3"
                label="What kind of music do you like?"
                :rules="[(v) => !!v || 'A response is required']"
              ></v-textarea>

              <div>
                <vue-recaptcha
                  :loadRecaptchaScript="true"
                  sitekey="6LdXKYUeAAAAAAuBfqXR3mpHMGxQw8NRUlTVcHT_"
                >
                  <v-btn large class="loginBtn" @click="signup">Sign up</v-btn>
                </vue-recaptcha>
              </div>
            </v-form>
          </v-col>
        </v-row>

        <v-row class="mt-2">
          <v-col>
            <h1 class="text-center">Or</h1>
          </v-col>
        </v-row>

        <v-row class="mt-2 mb-2">
          <v-col align="center" justify="center">
            <v-btn large class="loginBtn" @click=""
              >Register with Invitation Code</v-btn
            >
          </v-col>
        </v-row>
      </div>
    </v-sheet>
  </v-container>
</template>

<script>
import { VueRecaptcha } from 'vue-recaptcha'
import { createWaitListSignup } from '@/services/api/WaitListService'

export default {
  name: 'WaitListView',
  components: {
    VueRecaptcha,
  },
  computed: {
    sheetWidth() {
      return this.$vuetify.breakpoint.smAndUp ? '40%' : '100%)'
    },
  },
  data: () => {
    return {
      valid: false,
      email: '',
      textResponse: '',
    }
  },
  methods: {
    signup() {
      if (!this.$refs.waitListForm.validate()) {
        return
      }

      createWaitListSignup(this.email, this.textResponse)
        .then(() => {})
        .catch(() => {})
    },
  },
}
</script>
