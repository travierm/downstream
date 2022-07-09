<template>
  <VueGlow
    :disabled="!canShowGlow"
    color="#FF0000"
    mode="hex"
    fade
    interval="20"
    intense
    rounded
    tile
  >
    <v-card class="ma-auto">
      <v-card-actions v-if="!hideActions">
        <v-spacer></v-spacer>

        <router-link
          v-if="mediaId"
          :to="{ path: `/discover/track/${videoId}` }"
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
      >
        <div
          style="width: 100%"
          class="black text-subtitle-1 pl-4 pt-4 pb-3 d-inline-block text-truncate youtubeCardTitle"
        >
          {{ cardTitle }}
        </div>
      </v-img>

      <!-- <div class="video-instance embed-responsive" :id="guid"></div> -->
    </v-card>
  </VueGlow>
</template>

<script>
import VueGlow from 'vue-glow'

// Components
import PushAction from './PushAction'
import CollectAction from './CollectAction'
import PlaylistAction from './PlaylistAction'

// Services
import Analytics from '../../services/api/AnalyticsService'
import YoutubePlayerManager from '../../services/YoutubePlayerManager'

import { mdiLayersSearch } from '@mdi/js'

window.$showCardGuids = false

export default {
  name: 'YoutubeCard',
  components: {
    VueGlow,
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
    canShowGlow() {
      return YoutubePlayerManager.currentPlayingGuid === this.guid
    },
  },
  data() {
    return {
      mdiLayersSearch,
      showThumbnail: true,
    }
  },
  methods: {
    handleDiscoverTrackClick() {},
    handleThumbnailClick() {
      // debugger
      this.$store.dispatch('player/playGuid', this.guid)
      Analytics.playedMedia(this.mediaId)
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
