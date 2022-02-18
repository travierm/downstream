<template>
  <v-app-bar app color="grey darken-4" dense dark fixed bottom>
    <div class="video-container rounded-lg">
      <div class="video-instance" id="downstream-video-container"></div>
    </div>

    <v-container>
      <v-row no-gutters class="justify-left">
        <v-col cols="auto" class="mr-1">
          <span>{{ playerQueueCount }}</span>
          <v-btn
            @click="replayCurrentCard"
            color="primary"
            icon
            class="ml-2 pa-1 mt-1"
            small
            ><v-icon large>{{ mdiReplay }}</v-icon></v-btn
          >
        </v-col>

        <v-col cols="auto">
          <v-btn
            v-if="onCollectionRoute"
            @click="shuffleCollection"
            color="secondary"
            class="focusBtn ml-2"
            ><v-icon>mdi-shuffle-variant</v-icon></v-btn
          >
        </v-col>

        <v-col cols="auto">
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
        </v-col>

        <v-col cols="auto">
          <v-btn
            @click="focusOnPlayingCard"
            color="secondary"
            class="focusBtn ml-2"
            ><v-icon class="pr-1" v-if="$vuetify.breakpoint.smAndUp"
              >mdi-magnify</v-icon
            >Focus</v-btn
          >
        </v-col>

        <v-col lg="2" v-if="$vuetify.breakpoint.smAndUp">
          <VolumeSlider v-on:update="changeVolume" class="ml-4" />
        </v-col>
      </v-row>
    </v-container>
  </v-app-bar>
</template>

<script>
import { mdiReplay } from '@mdi/js'

import VolumeSlider from './VolumeSlider'
import YoutubePlayerManager from '../services/YoutubePlayerManager'

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
    }
  },
  methods: {
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
      const playingCardGuid = YoutubePlayerManager.getPlayingCardId()

      if (playingCardGuid) {
        this.$vuetify.goTo('#' + playingCardGuid, {
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
    changeVolume(value) {
      _.debounce(() => {
        YoutubePlayerManager.setVolume(value)
      }, 450)()
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
  position: relative;
  width: 350px;
  height: 350px;
  bottom: 150px;
  left: -16px;
  background-color: black;
  display: flex;
  visibility: hidden;
}

.video-instance {
}
</style>
