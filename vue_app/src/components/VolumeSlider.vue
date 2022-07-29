<template>
  <v-slider
    v-model="value"
    class="volumeSlider"
    max="100"
    min="0"
    hide-details
    @click:prepend="toggleMute"
    :prepend-icon="volumeIcon"
  ></v-slider>
</template>

<script>
import _ from 'lodash'
import YoutubePlayerManager from '../services/YoutubePlayerManager'

export default {
  name: 'VolumeSlider',
  components: {},
  mounted() {
    YoutubePlayerManager.onVolumeChange(this.volmueChangeHandler)
  },
  data: () => {
    return {
      value: YoutubePlayerManager.volume,
      playerValue: YoutubePlayerManager.volume,
    }
  },
  computed: {
    volumeIcon() {
      return this.value !== 0 ? 'mdi-volume-high' : 'mdi-volume-variant-off'
    },
  },
  methods: {
    getIcon(value) {},
    volmueChangeHandler(volume) {
      this.playerValue = volume
      this.value = volume
    },
    toggleMute() {
      YoutubePlayerManager.toggleMute()
    },
  },
  watch: {
    value(newValue) {
      YoutubePlayerManager.setVolume(newValue)
    },
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss">
.volumeSlider {
  .v-slider {
    cursor: grab;
  }
}
</style>
