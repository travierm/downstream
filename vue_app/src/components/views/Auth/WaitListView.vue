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
            <h2>Use invitation code or join our waiting list</h2>
            <div class="text-body-1 mt-2">
              Downstream is not currently open to registration without an invite
              code.
            </div>
            <div class="text-body-1 mt-2">
              Please sign up on the wait list and will get you an account as
              soon as possible.
            </div>
          </v-col>
        </v-row>

        <v-row class="mt-2">
          <v-col align="center" justify="center">
            <v-btn large class="loginBtn" @click=""
              >Register with Invitation Code</v-btn
            >
          </v-col>
        </v-row>

        <v-row>
          <v-col>
            <h1 class="text-center">Or</h1>

            <alert-list
              style="margin-bottom: 0px"
              class="mt-2"
              ref="alertList"
            ></alert-list>
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
                :rules="[
                  (v) => !!v || 'Email is required',
                  (v) =>
                    !v ||
                    /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,4})+$/.test(v) ||
                    'Email must be valid',
                ]"
                required
                @change="this.$refs.alertList.clear()"
              ></v-text-field>

              <v-textarea
                name="input-7-1"
                outlined
                v-model="textResponse"
                rows="3"
                label="What kind of music do you like?"
                :rules="[(v) => !!v || 'A response is required']"
                @change="this.$refs.alertList.clear()"
              ></v-textarea>

              <div>
                <vue-recaptcha
                  @verify="signup"
                  :loadRecaptchaScript="true"
                  sitekey="6LdXKYUeAAAAAAuBfqXR3mpHMGxQw8NRUlTVcHT_"
                >
                  <v-btn large class="loginBtn">Join the Wait List</v-btn>
                </vue-recaptcha>
              </div>
            </v-form>
          </v-col>
        </v-row>
      </div>
    </v-sheet>
  </v-container>
</template>

<script>
import { VueRecaptcha } from 'vue-recaptcha'

import AlertList from '@/components/AlertList'
import { createWaitListSignup } from '@/services/api/WaitListService'

export default {
  name: 'WaitListView',
  components: {
    AlertList,
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
      this.$refs.alertList.clear()

      if (!this.$refs.waitListForm.validate()) {
        return
      }

      createWaitListSignup(this.email, this.textResponse)
        .then((response) => {
          this.$refs.alertList.create('success', response.data.message)
        })
        .catch((response) => {
          this.$refs.alertList.create('error', response.data.message)
        })
    },
  },
}
</script>
