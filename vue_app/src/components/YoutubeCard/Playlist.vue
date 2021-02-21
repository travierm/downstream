<template>
  <div>
    <div class="ml-4 mt-2" v-if="playlists.length <= 0">
      <h3>No playlists created yet..</h3>
    </div>
    <v-list flat v-else>
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

          <v-btn
            icon
            class="removeBtn"
            @click.stop.prevent="confirmPlaylistDelete(item)"
          >
            <v-icon class="ml-1" color="red">{{ mdiMinusCircle }}</v-icon>
          </v-btn>
        </v-list-item>

        <ConfirmDialog
          :show="showConfirmDialog"
          message="Are you sure you want to delete this playlist?"
          v-on:closed="showConfirmDialog = false"
          v-on:confirmed="deleteList(selectedItem)"
        />
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
import { mapState } from 'vuex'
import {
  createPlaylist,
  addPlaylistItem,
  deletePlaylist,
  deletePlaylistItem,
} from '../../services/api/PlaylistService'
import {
  mdiCheckboxIntermediate,
  mdiCheckboxBlankOutline,
  mdiMinusCircle,
} from '@mdi/js'
import ConfirmDialog from '../Shared/ConfirmDialog'

export default {
  name: 'Playlist',
  components: {
    ConfirmDialog,
  },
  computed: {
    ...mapState({
      playlists: state => state.playlist.playlists,
    }),
  },
  props: {
    mediaId: {
      type: Number,
      default: 0,
      required: false,
    },
  },
  data: () => {
    return {
      selectedItem: false,
      listNameInput: '',
      showConfirmDialog: false,
      mdiMinusCircle,
      mdiCheckboxIntermediate,
      mdiCheckboxBlankOutline,
    }
  },
  methods: {
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
          this.$store.dispatch('playlist/getAll')
        })
        .catch(err => {
          console.error(err)
        })
    },
    confirmPlaylistDelete(item) {
      this.selectedItem = item
      this.showConfirmDialog = true
    },
    deleteList(item) {
      this.showConfirmDialog = false

      deletePlaylist(item.id)
        .then(() => {
          this.$store.dispatch('playlist/getAll', this.mediaId)
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
            this.$store.dispatch('playlist/getAll', this.mediaId)
          })
          .catch(err => {
            console.error(err)
          })
      } else {
        addPlaylistItem(item.id, this.mediaId)
          .then(() => {
            item.itemAdded = true
            this.$store.dispatch('playlist/getAll', this.mediaId)
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
