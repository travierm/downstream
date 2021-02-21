<template>
  <v-select
    item-text="name"
    item-id="id"
    :items="playlists"
    label="Playlist"
    solo
    dense
    light
    hide-details
    v-model="selectedPlaylist"
    return-object
    clearable
    @change="updateSelectedPlaylist"
  >
  </v-select>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: 'PlaylistSelect',
  computed: {
    ...mapState({
      playlists: state => state.playlist.playlists,
    }),
  },
  data() {
    return {
      selectedPlaylist: false
    }
  },
  methods: {
    async updateSelectedPlaylist() {
      if(this.selectedPlaylist) {
        this.$store.dispatch('playlist/setSelectedPlaylist', this.selectedPlaylist.id)

        await this.$store.dispatch('collection/fetchCollection', this.selectedPlaylist.id)
        this.$store.dispatch('collection/updateGuidIndex')
      }else{
        this.$store.dispatch('playlist/setSelectedPlaylist', false)

        await this.$store.dispatch('collection/fetchCollection')
        this.$store.dispatch('collection/updateGuidIndex')
      }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
