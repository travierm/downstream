<template>
  <v-container fluid :class="containerClass">
    <v-sheet color="grey darken-4" elevation="1">
      <v-row class="ml-1">
        <!-- <v-col cols="auto">
                    <v-btn color="primary">Playlists</v-btn>
                </v-col> -->

        <v-col :cols="mobileBreakpoint ? '12' : 'auto'">
          <CollectionSearchInput :class="mobileBreakpoint ? 'mr-4' : ''" />
        </v-col>

        <v-col cols="auto">
          <v-btn
            color="primary"
            lg
            @click="$store.dispatch('setPlaylistDrawerStatus', true)"
          >
            <v-icon class="pr-1">{{ mdiPlaylistMusic }}</v-icon>
            Playlists</v-btn
          >
        </v-col>

        <v-col cols="auto" v-if="!mobileBreakpoint">
          <div class="title">Collection Size: {{ collection.length }}</div>
        </v-col>

        <!-- <v-col cols="auto">
                    <div class="title">Playlists: {{ 0 }}</div>
                </v-col> -->
      </v-row>
    </v-sheet>
  </v-container>
</template>

<script>
import { mapState } from 'vuex'
import { mdiPlaylistMusic } from '@mdi/js'
import PlaylistSelect from '../PlaylistSelect'

import CollectionSearchInput from '@/components/Collection/CollectionSearchInput'

export default {
  name: 'CollectionBar',
  components: {
    PlaylistSelect,
    CollectionSearchInput,
  },
  computed: {
    containerClass() {
      return this.mobileBreakpoint ? 'pl-0 pr-0' : 'pa-2'
    },
    mobileBreakpoint() {
      return this.$vuetify.breakpoint.mobile
    },
    ...mapState({
      collection: state => state.collection.collection,
    }),
  },
  data() {
    return {
      mdiPlaylistMusic,
    }
  },
  watch: {},
  methods: {},
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
