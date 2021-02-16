<template>
  <v-btn class="collectBtn" icon color="blue lighten-1">
    <v-icon class="ml-1">{{ mdiPlaylistMusic }}</v-icon>

    <MoodListDialog />
  </v-btn>
</template>

<script>
import { mdiPlaylistMusic } from '@mdi/js'
import MoodListDialog from './MoodListDialog'

export default {
  name: 'MoodListAction',
  components: {
    MoodListDialog,
  },
  props: {
    mediaId: {
      type: Number,
      default: 0,
      required: false,
    },
  },
  data() {
    return {
      mdiPlaylistMusic,
    }
  },
  methods: {},
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
