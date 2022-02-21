<template>
  <v-container fluid>
    <v-row>
      <v-col>
        <CollectionBar v-if="hasCollectionItems" />

        <!-- Mobile Playlist Button -->
        <div class="mr-4 ml-4">
          <v-btn
            v-if="mobileBreakpoint"
            color="primary"
            block
            @click="$store.dispatch('setPlaylistDrawerStatus', true)"
          >
            <v-icon class="pr-1">{{ mdiPlaylistMusic }}</v-icon>
            Playlists</v-btn
          >
        </div>
      </v-col>
    </v-row>

    <v-row v-if="hasCollectionItems">
      <CardCol v-for="item in collection" :key="item.guid">
        <v-lazy :options="{ threshold: 0.5 }" transition="fade-transition">
          <YoutubeCard
            :item="item"
            :guid="item.guid"
            :title="item.title"
            :mediaId="item.media_id"
            :videoId="item.index"
            :thumbnail="item.thumbnail"
            :collected="item.collected"
            :key="searchQueryUpdates"
          ></YoutubeCard>
        </v-lazy>
      </CardCol>
    </v-row>

    <v-row v-if="!hasCollectionItems" class="justify-center mb-6">
      <h1>Search for a song you like above to start your collection!</h1>
    </v-row>

    <v-row v-if="!hasCollectionItems" class="justify-center">
      <img
        class="rounded mt-6"
        src="https://media.giphy.com/media/6uGhT1O4sxpi8/giphy.gif"
        width="480"
        height="240"
      />
    </v-row>
  </v-container>
</template>

<script>
import { mapGetters } from 'vuex'

import { mdiPlaylistMusic } from '@mdi/js'
import CardCol from '@/components/CardCol'
import BottomBar from '@/components/BottomBar'
import YoutubeCard from '@/components/YoutubeCard/YoutubeCard'
import CollectionBar from '@/components/Collection/CollectionBar'

export default {
  name: 'CollectionView',
  components: {
    CardCol,
    BottomBar,
    YoutubeCard,
    CollectionBar,
  },
  computed: {
    hasCollectionItems() {
      return this.collection.length >= 1
    },
    ...mapGetters({
      guidIndex: 'collection/guidIndex',
      collection: 'collection/collectionSearchResults',
      searchQueryUpdates: 'collection/searchQueryUpdates',
    }),
    mobileBreakpoint() {
      return this.$vuetify.breakpoint.mobile
    },
  },
  data() {
    return {
      mdiPlaylistMusic,
    }
  },
}
</script>
