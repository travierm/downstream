<template>
  <v-navigation-drawer
    v-if="drawerOpen"
    v-model="drawerOpen"
    temporary
    color="#1e1e1e"
  >
    <v-list>
      <v-list-item link>
        <v-list-item-title class="text-h6">Playlists</v-list-item-title>
      </v-list-item>
    </v-list>

    <v-divider></v-divider>

    <v-list nav density="compact">
      <v-list-item
        link
        v-for="playlist in playlistsWithCounts"
        :key="playlist.id"
        @click="updateSelectedPlaylist(playlist)"
      >
        <template #prepend><v-icon>$mdi-playlist-music</v-icon></template>
        <v-list-item-title>{{ playlist.name }}</v-list-item-title>
        <template #append><span class="text-caption">{{ playlist.count }}</span></template>
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
        ><template #prepend><v-icon>$mdi-album</v-icon></template>
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
      playlistDrawerStatus: (state) => state.playlistDrawerStatus,
      playlists: (state) => state.playlist.playlists,
    }),
    playlistsWithCounts() {
      return this.playlists.filter((list) => {
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
    playlistDrawerStatus: function (val) {
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
        this.$router.push({
          path: '/collection',
          query: { playlist_id: this.selectedPlaylist.id },
        })

        this.$store.dispatch(
          'playlist/setSelectedPlaylist',
          this.selectedPlaylist.id
        )

        await this.$store.dispatch(
          'collection/fetchCollection',
          this.selectedPlaylist.id
        )
      } else {
        this.selectedPlaylist = false
        this.$store.dispatch('playlist/setSelectedPlaylist', false)

        await this.$store.dispatch('collection/fetchCollection')
      }

      this.closeDrawer()
    },
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
