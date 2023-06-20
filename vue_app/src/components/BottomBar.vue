<template>
  <div>
    <div @click="togglePlayerFullscreen" class="overlay" v-if="isPlayerFullscreen"></div>
    <div class="rounded-lg"
      :class="{ 'video-container-fullscreen': isPlayerFullscreen, 'video-container': !isPlayerFullscreen }">
      <div class="video-instance" id="downstream-video-container"></div>
    </div>

    <v-app-bar app color="grey darken-4" dense dark fixed bottom>
      <v-container>
        <v-row no-gutters class="justify-left">

          <v-col cols="auto" class="mr-1">
            <span>{{ playerQueueCount }}</span>
            <v-btn @click="replayCurrentCard" color="primary" icon class="ml-2 pa-1 mt-1" small><v-icon large>{{ mdiReplay
            }}</v-icon></v-btn>
          </v-col>

          <v-col cols="auto">
            <v-btn @click="togglePlayerFullscreen" color="primary"
              class="focusBtn ml-2"><v-icon>mdi-fullscreen</v-icon></v-btn>
          </v-col>

          <!-- <v-col cols="auto">
          <v-btn
            v-if="onCollectionRoute"
            @click="shuffleCollection"
            color="primary"
            class="focusBtn ml-2"
            ><v-icon>mdi-shuffle-variant</v-icon></v-btn
          >
        </v-col> -->

          <!-- <v-col cols="auto">
          <v-btn
            class="ml-2"
            color="primary"
            lg
            @click="$store.dispatch('setPlaylistDrawerStatus', true)"
          >
            <v-icon class="pr-1" v-if="$vuetify.breakpoint.smAndUp"
              >mdi-playlist-music</v-icon
            >
            Playlists</v-btn
          >
        </v-col> -->

          <v-col cols="auto">
            <v-btn @click="focusOnPlayingCard" color="primary" class="focusBtn ml-2"><v-icon class="pr-1"
                v-if="$vuetify.breakpoint.smAndUp">mdi-magnify</v-icon>Focus</v-btn>
          </v-col>

          <v-col lg="2" v-if="$vuetify.breakpoint.smAndUp">
            <VolumeSlider class="ml-4" />
          </v-col>
        </v-row>
      </v-container>
    </v-app-bar>
  </div>
</template>

<script>
import { mdiReplay } from '@mdi/js'

import VolumeSlider from './VolumeSlider'
import YoutubePlayerManager from '../services/YoutubePlayerManager'
import { getPlayerSizeByCategory } from '../services/api/ScreenSizeService'

export default {
  name: 'BottomBar',
  components: {
    VolumeSlider,
  },
  computed: {
    playerQueueCount() {
      return this.manager.guidQueue.length
    },
    onCollectionRoute() {
      return this.$route.path == '/collection'
    },
  },
  data() {
    return {
      mdiReplay,
      manager: YoutubePlayerManager,
      isPlayerFullscreen: false,
    }
  },
  methods: {
    togglePlayerFullscreen() {
      this.isPlayerFullscreen = !this.isPlayerFullscreen

      const size = this.isPlayerFullscreen ? getPlayerSizeByCategory() : getPlayerSizeByCategory('sm')
      YoutubePlayerManager.setPlayerSize(size.width, size.height)
    },
    replayCurrentCard() {
      const currentPlayingGuid = this.manager.currentPlayingGuid

      if (currentPlayingGuid) {
        this.manager.queueNextCard(currentPlayingGuid)
      }
    },
    shuffleCollection() {
      this.$store.dispatch('collection/shuffle')
      this.$vuetify.goTo('#app', {
        offset: 150,
        duration: 800,
        easing: 'easeInOutCubic',
      })
    },
    focusOnPlayingCard() {
      const playingCardGuid = YoutubePlayerManager.getPlayingGuid()

      if (playingCardGuid) {
        this.$vuetify.goTo('#' + playingCardGuid + '_media', {
          offset: 150,
          duration: 800,
          easing: 'easeInOutCubic',
        })
      } else {
        this.$vuetify.goTo('#app', {
          offset: 0,
          duration: 800,
          easing: 'easeInOutCubic',
        })
      }
    },
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.bottom-bar {
  position: absolute;
  background-color: #4a52e8;
  /* background-color: white; */
  bottom: 0%;
}

.video-container {
  position: fixed;
  bottom: 0%;
  left: 0%;
  width: 356px;
  height: 200px;
  background-color: black;
  visibility: hidden;
  z-index: 9999;
}

.video-container-fullscreen {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  /* width: 640px;
  height: 360px; */
  background-color: black;
  visibility: visible;
  overflow: hidden;
  z-index: 9999;
  /* Adjust the z-index value as needed */
}

.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.8);
  /* Adjust the opacity to your preference */
  z-index: 999;
  /* Ensure the overlay appears above other elements */
}</style>
