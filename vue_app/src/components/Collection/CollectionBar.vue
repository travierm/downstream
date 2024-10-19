<template>
  <v-container fluid :class="containerClass" class="mt-2">
    <v-row v-if="!mobileBreakpoint">
      <v-col :cols="mobileBreakpoint ? '12' : '4'">
        <CollectionSearchInput :collection-count="collection.length" :class="mobileBreakpoint ? 'mr-4' : ''" />
      </v-col>
    </v-row>

    <CollectionSearchInput :collection-count="collection.length" v-else class="ml-4 mr-4" />

    <v-chip-group>
      <v-chip>Collection By Date</v-chip>

      <v-chip>Collection with Push History</v-chip>
    </v-chip-group>
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
      collection: (state) => state.collection.collection,
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
