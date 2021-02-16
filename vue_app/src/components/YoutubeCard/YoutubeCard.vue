<template>
  <v-card class="mx-auto" max-width="580">
    <v-card-actions v-if="!hideActions">
      <v-spacer></v-spacer>

      <PushAction :mediaId="mediaId" />

      <MoodListAction v-if="collected" :mediaId="mediaId" />

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
        style="width: 90%;"
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
import MoodListAction from './MoodListAction'

// Services
import YouTubeCardPlayer from '../../services/YouTubeCardPlayer'

window.$showCardGuids = false

export default {
  name: 'YoutubeCard',
  components: {
    PushAction,
    CollectAction,
    MoodListAction,
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
      showThumbnail: true,
    }
  },
  mounted() {
    this.registerCardPlayer()
  },
  methods: {
    handleVideoPlay() {
      this.showThumbnail = false

      $('#' + this.guid).show()
      this.$set(this, 'showThumbnail', false)
    },
    handleVideoStop() {
      this.showThumbnail = true

      $('#' + this.guid).hide()
    },
    handleThumbnailClick() {
      this.cardPlayer.play()
    },
    registerCardPlayer() {
      this.cardPlayer = new YouTubeCardPlayer(this.guid, this.videoId)

      // Register events so we can update our view on player state changes
      this.cardPlayer.on('play', () => {
        this.handleVideoPlay()
      })

      // Reset video once it stops
      this.cardPlayer.on('ended', () => {
        this.handleVideoStop()
      })

      this.cardPlayer.on('stopped_by_manager', () => {
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
</style>
