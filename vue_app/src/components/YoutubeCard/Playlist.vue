<template>
  <div>
    <v-list flat>
      <v-list-item-group dark color="white" :ripple="false">
        <v-list-item
          exact-active-class="no-active"
          v-for="item in playlists"
          :key="item.id"
          @click="clickedListItem(item)"
        >
          <v-list-item-icon>
            <v-icon>{{
              item.itemAdded ? mdiCheckboxIntermediate : mdiCheckboxBlankOutline
            }}</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title v-text="item.name"></v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-item-group>
    </v-list>

    <v-divider dark></v-divider>

    <div class="ml-4 mr-4 mt-4">
      <v-text-field
        solo
        light
        dense
        v-model="listNameInput"
        label="Create new Playlist"
        required
        clearable
        hide-details
      ></v-text-field>

      <v-btn @click="createList" class="mt-2" color="primary">Create</v-btn>
    </div>
  </div>
</template>

<script>
import _ from 'lodash'
import {
  getAllPlaylists,
  createPlaylist,
  addPlaylistItem,
  deletePlaylist,
  deletePlaylistItem,
} from '../../services/api/PlaylistService'
import { mdiCheckboxIntermediate, mdiCheckboxBlankOutline } from '@mdi/js'

export default {
  name: 'Playlist',
  components: {},
  props: {
    mediaId: {
      type: Number,
      default: 0,
      required: false,
    },
  },
  data: () => {
    return {
      listNameInput: '',
      mdiCheckboxIntermediate,
      mdiCheckboxBlankOutline,
      playlists: [],
    }
  },
  mounted() {
    this.fetchAllPlaylists()
  },
  methods: {
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
    createList() {
      if (this.listNameInput == '') {
        return
      }

      const existingItem = _.find(this.lists, { name: this.listNameInput })
      if (existingItem) {
        return
      }

      createPlaylist(this.listNameInput)
        .then(() => {
          this.listNameInput = ''
          this.fetchAllPlaylists()
        })
        .catch(err => {
          console.error(err)
        })
    },
    clickedListItem(item) {
      if (item.itemAdded) {
        deletePlaylistItem(item.id, this.mediaId)
          .then(() => {
            item.itemAdded = false
          })
          .catch(err => {
            console.error(err)
          })
      } else {
        addPlaylistItem(item.id, this.mediaId)
          .then(() => {
            item.itemAdded = true
          })
          .catch(err => {
            console.error(err)
          })
      }
    },
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
no-active::before {
  opacity: 0 !important;
}
</style>
