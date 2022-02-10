<template>
  <v-container>
    <v-row>
      <v-col cols="12" sm="3">
        <h1>
          <v-icon x-large color="green" class="mr-1 mb-1">mdi-spotify</v-icon>
          <span class="mb-1">Spotify Setup</span>
        </h1>

        <v-btn class="mt-2" @click="getUrl">Setup</v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import http from '../../../services/api/Client'

export default {
  name: 'SpotifySetup',
  components: {},
  data: () => {
    return {}
  },
  methods: {
    getUrl() {
      http
        .get('/spotify/url')
        .then(response => {
          window.open(response.data.url, '_blank')
        })
        .catch(err => {})
    },
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
