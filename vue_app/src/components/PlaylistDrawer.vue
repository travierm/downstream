<template>
  <v-navigation-drawer
    v-if="drawerOpen"
    v-model="drawerOpen"
    absolute
    temporary
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
        v-for="playlist in playlists"
        :key="playlist.id"
        @click="updateSelectedPlaylist(playlist)"
      >
        <v-list-item-icon>
          <v-icon>mdi-playlist-music</v-icon>
        </v-list-item-icon>
        <v-list-item-title>{{ playlist.name }}</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>

<script>
import { mapState } from 'vuex'
import { mdiPlaylistMusic } from '@mdi/js'

export default {
  name: 'PlaylistDrawer',
  props: ['drawer'],
  components: {},
  computed: {
    ...mapState({
      playlistDrawerStatus: state => state.playlistDrawerStatus,
      playlists: state => {
        return state.playlist.playlists.filter(list => {
          return list.count >= 1
        })
      },
    }),
  },
  data() {
    return {
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
