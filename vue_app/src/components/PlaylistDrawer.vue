<template>
  <v-navigation-drawer
    v-if="drawerOpen"
    v-model="drawerOpen"
    fixed
    temporary
    dark
    color="dark"
  >
    <v-list>
      <v-list-item link>
        <v-list-item-content>
          <v-list-item-title class="title">
            Playlists
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>

    <v-divider></v-divider>

    <v-list nav dense>
      <v-list-item
        link
        v-for="playlist in playlistsWithCounts"
        :key="playlist.id"
        @click="updateSelectedPlaylist(playlist)"
      >
        <v-list-item-icon>
          <v-icon>mdi-playlist-music</v-icon>
        </v-list-item-icon>
        <v-list-item-title>{{ playlist.name }}</v-list-item-title>
        <v-list-item-action-text>{{ playlist.count }}</v-list-item-action-text>
      </v-list-item>

      <v-list-item v-if="playlists.length <= 0">
        <v-list-item-title>No Playlists Created</v-list-item-title>
      </v-list-item>

      <v-list-item
        v-if="playlists.length >= 1 && playlistsWithCounts.length <= 0"
      >
        <v-list-item-title>All playlists are empty</v-list-item-title>
      </v-list-item>

      <v-list-item link @click="showCollection"
        ><v-list-item-icon><v-icon>mdi-all-inclusive</v-icon></v-list-item-icon>
        <v-list-item-title>View Collection</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>

<script>
import { mapState } from 'vuex'
import { mdiPlaylistMusic, mdiAllInclusive } from '@mdi/js'

export default {
  name: 'PlaylistDrawer',
  props: ['drawer'],
  components: {},
  computed: {
    ...mapState({
      playlistDrawerStatus: state => state.playlistDrawerStatus,
      playlists: state => state.playlist.playlists,
    }),
    playlistsWithCounts() {
      return this.playlists.filter(list => {
        return list.count >= 1
      })
    },
  },
  data() {
    return {
      mdiAllInclusive,
      mdiPlaylistMusic,
      selectedPlaylist: false,
      drawerOpen: this.playlistDrawerStatus,
    }
  },
  mounted() {},
  watch: {
    playlistDrawerStatus: function(val) {
      this.drawerOpen = val
    },
    drawerOpen(val) {
      if (val !== this.playlistDrawerStatus) {
        this.$store.dispatch('setPlaylistDrawerStatus', val)
      }
    },
  },
  methods: {
    openDrawer() {
      this.$store.dispatch('setPlaylistDrawerStatus', true)
    },
    closeDrawer() {
      this.$store.dispatch('setPlaylistDrawerStatus', false)
    },
    async showCollection() {
      await this.$store.dispatch('collection/fetchCollection')
      this.$store.dispatch('collection/updateGuidIndex')

      this.closeDrawer()
    },
    async updateSelectedPlaylist(playlist) {
      this.selectedPlaylist = playlist

      if (this.selectedPlaylist) {
        this.$store.dispatch(
          'playlist/setSelectedPlaylist',
          this.selectedPlaylist.id
        )

        await this.$store.dispatch(
          'collection/fetchCollection',
          this.selectedPlaylist.id
        )
        this.$store.dispatch('collection/updateGuidIndex')
      } else {
        this.selectedPlaylist = false
        this.$store.dispatch('playlist/setSelectedPlaylist', false)

        await this.$store.dispatch('collection/fetchCollection')
        this.$store.dispatch('collection/updateGuidIndex')
      }

      this.closeDrawer()
    },
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
