<template>
  <v-slider
    v-model="value"
    class="volumeSlider"
    max="100"
    min="0"
    hide-details
    prepend-icon="mdi-volume-high"
  ></v-slider>
</template>

<script>
import _ from 'lodash'
import YoutubePlayerManager from '../services/YoutubePlayerManager'

// TODO WHEN MUTED IT TOGGLES BACK TO FULL VOLUME NEED A WAY TO CHECK IF THE PLAYER IS MUTED

export default {
  name: 'VolumeSlider',
  components: {},
  mounted() {
    const volmueChangeHandler = (volume) => {
      this.playerValue = volume
      this.value = volume
    }

    YoutubePlayerManager.onVolumeChange(volmueChangeHandler)
  },
  data: () => {
    return {
      value: YoutubePlayerManager.volume,
      playerValue: YoutubePlayerManager.volume,
    }
  },
  watch: {
    value(newValue) {
      if (this.playerValue != this.value) {
        YoutubePlayerManager.setVolume(newValue)
      }
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
