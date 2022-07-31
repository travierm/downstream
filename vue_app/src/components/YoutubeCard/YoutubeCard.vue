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
    opacity: 0;
    border-radius: 8px;

    // Adjust the speed by changing the seconds - currently at 1 color per 1.5 seconds
    animation: glowing-colorful 150s linear infinite;
    // animation: glowing 15s linear infinite;

    // Control the size of the glow
    top: -6px;
    left: -6px;
    width: calc(100% + 12px);
    height: calc(100% + 12px);
  }
}

@keyframes glowing {
  0% {
    background-color: #20b2aa;
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
    background-color: #cd5c5c;
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
@keyframes glowing-colorful {
  0% {
    background-color: #808080;
  }

  1% {
    background-color: #ff3131;
  }

  2% {
    background-color: #fff01f;
  }

  3% {
    background-color: #39ff14;
  }

  4% {
    background-color: #1f51ff;
  }

  5% {
    background-color: #bc13fe;
  }

  6% {
    background-color: #ff1493;
  }

  7% {
    background-color: #ffad00;
  }

  8% {
    background-color: #734e46;
  }

  9% {
    background-color: #005f61;
  }

  10% {
    background-color: #fffdd0;
  }

  11% {
    background-color: #ff3131;
  }

  12% {
    background-color: #fff01f;
  }

  13% {
    background-color: #7fff00;
  }

  14% {
    background-color: #0ff0fc;
  }

  15% {
    background-color: #8a2be2;
  }

  16% {
    background-color: #ff44cc;
  }

  17% {
    background-color: #ffb673;
  }

  18% {
    background-color: #624a2e;
  }

  19% {
    background-color: #006778;
  }

  20% {
    background-color: #b5bda5;
  }

  21% {
    background-color: #c54644;
  }

  22% {
    background-color: #e7ee4f;
  }

  23% {
    background-color: #ccff00;
  }

  24% {
    background-color: #05c3dd;
  }

  25% {
    background-color: #8031a7;
  }

  26% {
    background-color: #ea00ff;
  }

  27% {
    background-color: #ff9913;
  }

  28% {
    background-color: #ab784e;
  }

  29% {
    background-color: #00b2a9;
  }

  30% {
    background-color: #8c92ac;
  }

  31% {
    background-color: #ab2328;
  }

  32% {
    background-color: #eaaa00;
  }

  33% {
    background-color: #aadb1e;
  }

  34% {
    background-color: #0827f5;
  }

  35% {
    background-color: #693c5e;
  }

  36% {
    background-color: #fe019a;
  }

  37% {
    background-color: #fc4c02;
  }

  38% {
    background-color: #b67233;
  }

  39% {
    background-color: #00677f;
  }

  40% {
    background-color: #d7d2cb;
  }

  41% {
    background-color: #cd001a;
  }

  42% {
    background-color: #ccff00;
  }

  43% {
    background-color: #00873e;
  }

  44% {
    background-color: #3c5291;
  }

  45% {
    background-color: #2e1a47;
  }

  46% {
    background-color: #ffbcd9;
  }

  47% {
    background-color: #cb6015;
  }

  48% {
    background-color: #623412;
  }

  49% {
    background-color: #009ca6;
  }

  50% {
    background-color: #787976;
  }

  51% {
    background-color: #9d2235;
  }

  52% {
    background-color: #f0e2b6;
  }

  53% {
    background-color: #4e5b31;
  }

  54% {
    background-color: #212e52;
  }

  55% {
    background-color: #c724b1;
  }

  56% {
    background-color: #de5d83;
  }

  57% {
    background-color: #e2522f;
  }

  58% {
    background-color: #b77729;
  }

  59% {
    background-color: #30ced8;
  }

  60% {
    background-color: #ffdead;
  }

  61% {
    background-color: #800500;
  }

  62% {
    background-color: #fedb00;
  }

  63% {
    background-color: #154734;
  }

  64% {
    background-color: #587ede;
  }

  65% {
    background-color: #9c50b6;
  }

  66% {
    background-color: #d95d67;
  }

  67% {
    background-color: #eb681a;
  }

  68% {
    background-color: #a94007;
  }

  69% {
    background-color: #81cdc6;
  }

  70% {
    background-color: #f3e5ab;
  }

  71% {
    background-color: #ff6d6a;
  }

  72% {
    background-color: #f7f749;
  }

  73% {
    background-color: #08ff08;
  }

  74% {
    background-color: #0000ff;
  }

  75% {
    background-color: #4b365f;
  }

  76% {
    background-color: #ff66cc;
  }

  77% {
    background-color: #fcd299;
  }

  78% {
    background-color: #774b3a;
  }

  79% {
    background-color: #025043;
  }

  80% {
    background-color: #fff5ee;
  }

  81% {
    background-color: #d22730;
  }

  82% {
    background-color: #f6be00;
  }

  83% {
    background-color: #00a86b;
  }

  84% {
    background-color: #5576d1;
  }

  85% {
    background-color: #bb29bb;
  }

  86% {
    background-color: #f1abb9;
  }

  87% {
    background-color: #d76b00;
  }

  88% {
    background-color: #964e02;
  }

  89% {
    background-color: #048c7f;
  }

  90% {
    background-color: #e9dcc9;
  }

  91% {
    background-color: #e10600;
  }

  92% {
    background-color: #e0a526;
  }

  93% {
    background-color: #01735c;
  }

  94% {
    background-color: #00aeef;
  }

  95% {
    background-color: #c6a1cf;
  }

  96% {
    background-color: #ffa6c9;
  }

  97% {
    background-color: #ff7b2e;
  }

  98% {
    background-color: #a0522d;
  }

  99% {
    background-color: #b3e0dc;
  }

  100% {
    background-color: #f9f6ee;
  }
}
</style>
<!-- https://dev.to/mysticza/beautiful-chase-rgb-glow-effect-css-1h2p -->
