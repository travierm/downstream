<template>
  <v-container>
    <h1>
      <v-icon x-large class="mr-1 mb-2" color="green">{{ mdiSpotify }}</v-icon
      >Spotify Sync
    </h1>

    <h3 v-if="hasSpotifyConnection" class="green--text">
      Your Spotify Account is connected with Downstream!
    </h3>
    <h3 v-if="!hasSpotifyConnection" class="amber--text">
      Your Spotify Account is not connected with Downstream.
    </h3>

    <v-btn
      v-if="!hasSpotifyConnection"
      @click="getAuthorizeUrl()"
      class="mt-2"
      depressed
      color="primary"
    >
      Connect Spotify Account
    </v-btn>
  </v-container>
</template>

<script>
import { mapState } from 'vuex'
import { mdiSpotify } from '@mdi/js'
import BottomBar from '@/components/BottomBar'
import { getAuthorizeUrl } from '../../services/api/spotify'

export default {
  name: 'SpotifySyncView',
  components: {
    BottomBar,
  },
  data: () => ({
    mdiSpotify,
  }),
  computed: {
    ...mapState({
      hasSpotifyConnection: (state) => state.auth.user.has_spotify_connection,
    }),
  },
  methods: {
    getAuthorizeUrl() {
      getAuthorizeUrl()
        .then((resp) => {
          window.open(resp.data, '_self')
        })
        .catch((err) => {
          throw err
        })
    },
  },
  watch: {},
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
