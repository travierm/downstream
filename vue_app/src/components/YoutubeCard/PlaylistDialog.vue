<template>
  <v-dialog v-model="showDialog" persistent max-width="400">
    <v-card>
      <v-card-title class="headline">
        Playlists
      </v-card-title>

      <v-card-text>
        <Playlist :mediaId="mediaId" :playlists='playlists' :updateLists='fetchAllPlaylists' />
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
import { getAllPlaylists } from '../../services/api/PlaylistService'

export default {
  name: 'PlaylistDialog',
  props: {},
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
      showDialog: this.show,
    }
  },
  watch: {
    show(propVal) {
      this.showDialog = propVal
    },
  },
  methods: {
    openDialog() {
      this.showDialog = true
      this.fetchAllPlaylists()
    },
    handleConfirm() {
      this.showDialog = false
      this.$emit('confirmed', true)
    },
    handleClose() {
      this.showDialog = false
      this.$emit('closed', true)
    },
    // API Methods
    fetchAllPlaylists() {
      getAllPlaylists(this.mediaId)
        .then(resp => {
          const items = resp.data.items
          if (items) {
            this.playlists = items
          }
        })
        .catch(err => {
          console.error(err)
        })
    },
  },
}
</script>
