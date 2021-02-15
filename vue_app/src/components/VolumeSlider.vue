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

export default {
  name: 'VolumeSlider',
  components: {},
  data: () => {
    return {
      value: YoutubePlayerManager.getVolume(),
    }
  },
  watch: {
    value(newValue) {
      this.$emit('update', newValue)
    },
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
