<template>
  <div>
    <div @click="togglePlayerFullscreen" class="overlay" v-if="isPlayerFullscreen"></div>
    <div :class="{ 'video-container-fullscreen': isPlayerFullscreen, 'video-container': !isPlayerFullscreen }">
      <div class="video-instance rounded" id="downstream-video-container"></div>
    </div>

    <v-app-bar class="player-bar" color="grey-darken-4" location="bottom"
      :height="$vuetify.display.smAndUp ? 64 : 96">
      <v-container class="py-0">
        <v-row no-gutters class="justify-left">

          <v-col cols="auto" class="mr-1">
            <span>{{ playerQueueCount }}</span>
            <v-btn @click="replayCurrentCard" color="primary" icon class="ml-2 pa-1 mt-1" size="small"><v-icon size="large">{{
              mdiReplay
                }}</v-icon></v-btn>
          </v-col>

          <v-col cols="auto">
            <v-btn @click="togglePlayerFullscreen" color="primary"
              class="focusBtn ml-2"><v-icon>$mdi-fullscreen</v-icon></v-btn>
          </v-col>

          <v-col cols="auto">
            <v-btn v-if="onCollectionRoute" @click="shuffleCollection" color="primary" class="focusBtn ml-2">
              <v-icon v-if="!shuffled">$mdi-shuffle-variant</v-icon>
              <v-icon v-else>$mdi-shuffle-disabled</v-icon>
            </v-btn>
          </v-col>

          <!-- <v-col cols="auto">
          <v-btn
            class="ml-2"
            color="primary"
            lg
            @click="$store.dispatch('setPlaylistDrawerStatus', true)"
          >
            <v-icon class="pr-1" v-if="$vuetify.display.smAndUp"
              >$mdi-playlist-music</v-icon
            >
            Playlists</v-btn
          >
        </v-col> -->

          <v-col cols="auto">
            <v-btn @click="focusOnPlayingCard" color="primary" class="focusBtn ml-2"><v-icon class="pr-1"
                v-if="$vuetify.display.smAndUp">$mdi-magnify</v-icon>Focus</v-btn>
          </v-col>

          <v-col lg="2" v-if="$vuetify.display.smAndUp">
            <VolumeSlider class="ml-4" />
          </v-col>
        </v-row>
      </v-container>

      <div class="player-links d-flex flex-row">
        <router-link to="/privacy" class="unstyled-link">
          Privacy
        </router-link>

        <router-link to="/tos" class="unstyled-link">
          Terms
        </router-link>

        <a class="unstyled-link" href="https://www.youtube.com/t/terms">YouTube Terms</a>
      </div>
    </v-app-bar>
  </div>
</template>

<script>
import { mdiReplay } from '@mdi/js'
import { useGoTo } from 'vuetify'

import VolumeSlider from './VolumeSlider'
import YoutubePlayerManager from '../services/YoutubePlayerManager'
import { getPlayerSizeByCategory } from '../services/api/ScreenSizeService'
import { mapState } from 'vuex'

export default {
  name: 'BottomBar',
  setup() {
    return { goTo: useGoTo() }
  },
  components: {
    VolumeSlider,
  },
  beforeUnmount() {
    YoutubePlayerManager.destroyPlayer()
  },
  computed: {
    ...mapState({
      shuffled: (store) => store.collection.shuffled,
    }),
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
        this.manager.queueNextGuid(currentPlayingGuid)
      }
    },
    shuffleCollection() {
      this.$store.dispatch('collection/shuffle')
      this.goTo('#app', {
        offset: 150,
        duration: 800,
        easing: 'easeInOutCubic',
      })
    },
    focusOnPlayingCard() {
      const playingCardGuid = YoutubePlayerManager.getPlayingGuid()

      if (playingCardGuid) {
        this.goTo('#' + playingCardGuid + '_media', {
          offset: 150,
          duration: 800,
          easing: 'easeInOutCubic',
        })
      } else {
        this.goTo('#app', {
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
.player-links {
  gap: 8px;
  white-space: nowrap;
  padding-right: 12px;
}

@media (max-width: 599px) {
  .player-bar :deep(.v-toolbar__content) {
    flex-direction: column;
    justify-content: center;
    gap: 8px;
  }

  .player-bar .v-row {
    flex-wrap: nowrap;
    justify-content: center;
    align-items: center;
  }

  .player-links {
    padding: 0;
    font-size: 12px;
  }
}

.bottom-bar {
  position: absolute;
  background-color: #4a52e8;
  /* background-color: white; */
  bottom: 0%;
}

.video-container {
  position: fixed;
  bottom: 0%;
  right: 0%;
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
}
</style>
