<template>
  <div class="youtube-card-glow" :class="{ 'youtube-card-glow--active': canShowGlow }">
    <v-card :class="{ 'ma-auto': true, 'youtube-card': true, light: this.light }" style="z-index: 1">
      <v-card-actions v-if="!hideActions">
        <!-- Spotify Tag -->
        <v-tooltip location="top" v-if="spotifyId">
          <template v-slot:activator="{ props }">
            <v-icon v-bind="props" class="float-left" color="green">{{
              mdiSpotify
            }}</v-icon>
          </template>
          <span>Video was synced from Spotify</span>
        </v-tooltip>

        <!-- Repeat Button -->
        <v-tooltip location="top">
          <template v-slot:activator="{ props }">
            <v-btn v-bind="props" icon color="blue-accent-3" @click="handleThumbnailClick">
              <v-icon>{{ mdiRepeat }}</v-icon>
            </v-btn>
          </template>
          <span>Restart the video </span>
        </v-tooltip>
        <span class="ml-2" v-if="plays"> {{ plays }} Plays</span>

        <v-spacer></v-spacer>

        <router-link v-if="mediaId" :to="{ path: `/discover/track/${videoId}` }">
          <v-tooltip location="top">
            <template v-slot:activator="{ props }">
              <v-btn v-bind="props" icon>
                <v-icon class="ml-1" color="deep-purple-accent-2">{{
                  mdiLayersSearch
                }}</v-icon>
              </v-btn>
            </template>
            <span>Discover similar media</span>
          </v-tooltip>
        </router-link>

        <!-- <AutoFixAction v-if="collected" :mediaId="mediaId" /> -->
        <PushAction v-if="collected" :mediaId="mediaId" />
        <!-- <PlaylistAction v-if="collected" :mediaId="mediaId" /> -->
        <CollectAction :videoId="videoId" :mediaId="mediaId" :collected="collected" />


      </v-card-actions>

      <v-img :id="this.guid + '_media'" class="youtubeCardThumbnail" :src="thumbnail" :height="cardHeight"
        @click="handleThumbnailClick" cover>
        <div style="width: 100%"
          class="bg-black text-subtitle-1 pl-4 pt-4 pb-3 d-inline-block text-truncate youtubeCardTitle">
          {{ cardTitle }}
        </div>
      </v-img>

      <CollectedByFooter v-if="collectedAt && collectedBy" :collected-at="collectedAt" :collected-by="collectedBy" />
      <!-- <div class="video-instance embed-responsive" :id="guid"></div> -->
    </v-card>
  </div>
</template>

<script>
// Components
import PushAction from './PushAction'
import CollectAction from './CollectAction'
import PlaylistAction from './PlaylistAction'
import AutoFixAction from './AutoFixAction'
import CollectedByFooter from './CollectedByFooter.vue'

// Services
import YoutubePlayerManager from '../../services/YoutubePlayerManager'

import { mdiSpotify, mdiLayersSearch, mdiRepeat } from '@mdi/js'

window.$showCardGuids = false

export default {
  name: 'YoutubeCard',
  components: {
    PushAction,
    CollectAction,
    PlaylistAction,
    CollectedByFooter
  },
  props: {
    title: String,
    mediaId: Number,
    videoId: String,
    collectedAt: String,
    collectedBy: String,
    plays: {
      type: Number,
      required: false,
    },
    spotifyId: {
      type: String,
      required: false,
    },
    thumbnail: String,
    hideActions: {
      type: Boolean,
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
    light: {
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
      cardHeight: '250px', // 435px was old value
      mdiSpotify,
      mdiLayersSearch,
      mdiRepeat,
      showThumbnail: true,
    }
  },
  methods: {
    handleThumbnailClick() {
      this.$store.dispatch('player/playGuid', this.guid)
    },
    handleAutoFix() {
      this.$store.dispatch('player/autoFix', this.mediaId)
    },
  },
}
</script>

<style scoped lang="scss">
.youtubeCardTitle {
  text-align: left;
}

.youtube-card {
  &.light {
    background-color: #424242;
  }
}

// Keep the gradients and blur static; only the playing card animates opacity.
.youtube-card-glow {
  position: relative;
  isolation: isolate;
  border-radius: 4px;

  &::after {
    content: none;
    position: absolute;
    pointer-events: none;
    border-radius: inherit;
    background: linear-gradient(125deg, #24b6ff 0%, #7c4dff 55%, #f472b6 100%);
  }

  &::after {
    inset: -5px;
    z-index: -1;
    filter: blur(12px);
    opacity: 0.5;
  }

  &--active::after {
    content: '';
    animation: youtube-card-glow-breathe 4s ease-in-out infinite alternate;
  }
}

@keyframes youtube-card-glow-breathe {
  from {
    opacity: 0.45;
  }

  to {
    opacity: 0.8;
  }
}

@media (prefers-reduced-motion: reduce) {
  .youtube-card-glow--active::after {
    animation: none;
    opacity: 0.65;
  }
}

@media (forced-colors: active) {
  .youtube-card-glow--active,
  .youtube-card-glow:focus-within {
    outline: 2px solid Highlight;
    outline-offset: 2px;
  }

  .youtube-card-glow::after {
    content: none;
  }
}
</style>
