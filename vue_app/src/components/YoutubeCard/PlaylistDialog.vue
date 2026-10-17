<template>
  <v-dialog v-model="showDialog" max-width="400">
    <v-card>
      <v-card-title class="text-h5">
        Playlists
      </v-card-title>

      <v-card-text>
        <Playlist :mediaId="mediaId" />
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn @click="handleClose">
          Close
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import Playlist from './Playlist'

export default {
  name: 'PlaylistDialog',
  emits: ['confirmed', 'closed'],
  components: {
    Playlist,
  },
  props: {
    mediaId: {
      type: Number,
      default: 0,
      required: false,
    },
  },
  data: function() {
    return {
      playlists: [],
      showDialog: false,
    }
  },
  methods: {
    openDialog() {
      this.showDialog = true
      this.$store.dispatch('playlist/getAll', this.mediaId)
    },
    handleConfirm() {
      this.showDialog = false
      this.$emit('confirmed', true)
    },
    handleClose() {
      this.showDialog = false
      this.$emit('closed', true)
    },
  },
}
</script>
