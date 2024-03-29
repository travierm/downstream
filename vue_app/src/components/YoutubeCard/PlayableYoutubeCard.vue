<template>
  <v-card class="ma-auto">
    <v-card-actions v-if="!hideActions">
      <v-spacer></v-spacer>

      <router-link
        v-if="mediaId"
        :to="{ path: `/discover/track/${videoId}` }"
        target="_blank"
      >
        <v-tooltip top>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              v-on="on"
              v-bind="attrs"
              icon
              @click="handleDiscoverTrackClick"
            >
              <v-icon class="ml-1" color="deep-purple accent-2">{{
                mdiLayersSearch
              }}</v-icon>
            </v-btn>
          </template>
          <span>Discover similar media</span>
        </v-tooltip>
      </router-link>

      <PushAction v-if="collected" :mediaId="mediaId" />
      <PlaylistAction v-if="collected" :mediaId="mediaId" />
      <CollectAction
        :videoId="videoId"
        :mediaId="mediaId"
        :collected="collected"
      />
    </v-card-actions>

    <v-img
      :id="this.guid + '_media'"
      class="youtubeCardThumbnail"
      :src="thumbnail"
      :height="dense ? '250px' : '435px'"
      @click="handleThumbnailClick"
      v-if="showThumbnail"
    >
      <div
        style="width: 90%"
        class="text-subtitle-1 pl-4 pt-3 d-inline-block text-truncate youtubeCardTitle"
      >
        {{ cardTitle }}
      </div>
    </v-img>

    <div class="video-instance embed-responsive" :id="guid"></div>
  </v-card>
</template>

<script>
import $ from 'jquery'
// Components
import PushAction from './PushAction'
import CollectAction from './CollectAction'
import PlaylistAction from './PlaylistAction'
// Services
import Analytics from '../../services/api/AnalyticsService'
import YouTubeCardPlayer from '../../services/YouTubeCardPlayer'
import { mdiLayersSearch } from '@mdi/js'

window.$showCardGuids = false
export default {
  name: 'YoutubeCard',
  components: {
    PushAction,
    CollectAction,
    PlaylistAction,
  },
  props: {
    guid: String,
    title: String,
    mediaId: Number,
    videoId: String,
    thumbnail: String,
    hideActions: {
      default: false,
    },
    guid: {
      type: String,
      required: true,
    },
    collected: {
      type: Boolean,
      default: false,
    },
    dense: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    cardTitle() {
      return window.$showCardGuids ? this.guid : this.title
    },
  },
  data() {
    return {
      mdiLayersSearch,
      showThumbnail: true,
    }
  },
  mounted() {
    this.registerCardPlayer()
  },
  methods: {
    handleDiscoverTrackClick() {
      this.$store.dispatch('player/stopPlayingCurrentCard')
    },
    handleVideoPlay() {
      this.showThumbnail = false
      $('#' + this.guid).show()
      this.$set(this, 'showThumbnail', false)
    },
    handleVideoStop() {
      this.showThumbnail = true
      $('#' + this.guid).hide()

      this.cardPlayer.stop()
    },
    handleThumbnailClick() {
      this.cardPlayer.play()
    },
    registerCardPlayer() {
      this.cardPlayer = new YouTubeCardPlayer(this.guid, this.videoId)

      // Register events so we can update our view on player state changes
      this.cardPlayer.on('play', () => {
        if (this.mediaId) {
          Analytics.playedMedia(this.mediaId)
        }
        this.handleVideoPlay()
      })
      // Reset video once it stops
      this.cardPlayer.on('ended', () => {
        this.handleVideoStop()
      })
      this.cardPlayer.on('paused', () => {})
      // When an error happens show the thumbnauk
      this.cardPlayer.on('unplayable', () => {
        this.handleVideoStop()
      })
    },
  },
}
</script>

<style>
.youtubeCardTitle {
  text-align: left;
}
.glowing-border {
  border: 2px solid #dadada;
  border-radius: 7px;
}
.glowing-border:focus {
  outline: none;
  border-color: #9ecaed;
  box-shadow: 0 0 10px #9ecaed;
}
</style>
