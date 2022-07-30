<template>
  <div :class="{ 'glow-border': true, 'glow-active': canShowGlow }">
    <v-card class="ma-auto" style="z-index: 1">
      <v-card-actions v-if="!hideActions">
        <!-- Spotify Tag -->
        <v-tooltip top v-if="spotifyId">
          <template v-slot:activator="{ on, attrs }">
            <v-icon v-bind="attrs" v-on="on" class="float-left" color="green">{{
              mdiSpotify
            }}</v-icon>
          </template>
          <span>Video was imported from Spotify</span>
        </v-tooltip>

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
  </div>
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

import { mdiSpotify, mdiLayersSearch } from '@mdi/js'

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
    spotifyId: {
      type: String,
      required: false,
    },
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
      mdiSpotify,
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

<style lang="scss">
.youtubeCardTitle {
  text-align: left;
}

.glow-border {
  position: relative;

  &.glow-active::before {
    opacity: 0.6;
  }

  &:before {
    content: '';
    position: absolute;
    background-size: 200%;
    z-index: 0;
    filter: blur(5px);
    animation: glowing 10s linear infinite;
    opacity: 0;
    border-radius: 8px;

    // Control the size of the glow
    top: -6px;
    left: -6px;
    width: calc(100% + 12px);
    height: calc(100% + 12px);
  }
}

@keyframes glowing {
  0% {
    background-color: #dd00f3;
  }
  12.5% {
    background-color: #ff2400;
  }
  25% {
    background-color: #e81d1d;
  }
  37.5% {
    background-color: #e8b71d;
  }
  50% {
    background-color: #e3e81d;
  }
  62.5% {
    background-color: #1de840;
  }
  75% {
    background-color: #1ddde8;
  }
  87.5% {
    background-color: #2b1de8;
  }
  100% {
    background-color: #dd00f3;
  }
}
</style>
<!-- https://dev.to/mysticza/beautiful-chase-rgb-glow-effect-css-1h2p -->
