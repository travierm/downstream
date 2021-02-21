<template>
  <v-btn class="collectBtn" @click="toggleDialog" icon color="blue lighten-1">
    <v-icon class="ml-1">{{ mdiPlaylistMusic }}</v-icon>

    <PlaylistDialog :mediaId='mediaId' ref="playlistDialog" />
  </v-btn>
</template>

<script>
import { mdiPlaylistMusic } from '@mdi/js'
import PlaylistDialog from './PlaylistDialog'

export default {
  name: 'PlaylistAction',
  components: {
    PlaylistDialog,
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
  methods: {
    toggleDialog() {
      this.$refs.playlistDialog.openDialog()
    }
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
