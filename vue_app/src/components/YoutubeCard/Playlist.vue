<template>
  <div>
    <div v-if="playlists.length <= 0" class="ml-4 mt-2">
      <h3>No playlists created yet..</h3>
    </div>
    <v-list v-else>
      <v-list-item
        v-for="item in playlists"
        :key="item.id"
        :active="false"
        :ripple="false"
        @click="clickedListItem(item)"
      >
        <template #prepend>
          <v-icon>{{ item.itemAdded ? mdiCheckboxIntermediate : mdiCheckboxBlankOutline }}</v-icon>
        </template>
        <v-list-item-title v-text="item.name" />
        <template #append>
          <v-btn icon class="removeBtn" @click.stop.prevent="confirmPlaylistDelete(item)">
            <v-icon class="ml-1" color="red">{{ mdiMinusCircle }}</v-icon>
          </v-btn>
        </template>
      </v-list-item>
      <ConfirmDialog
        :show="showConfirmDialog"
        message="Are you sure you want to delete this playlist?"
        @closed="showConfirmDialog = false"
        @confirmed="deleteList(selectedItem)"
      />
    </v-list>

    <v-divider />
    <div class="ml-4 mr-4 mt-4">
      <v-text-field
        v-model="listNameInput"
        variant="solo"
        theme="light"
        density="compact"
        label="Create new Playlist"
        required
        clearable
        hide-details
      />
      <v-btn class="mt-2" color="primary" @click="createList">Create</v-btn>
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
