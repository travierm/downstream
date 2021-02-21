<template>
  <v-container fluid>
    <v-row>
      <v-col>
        <!-- Mobile Playlist Button -->
        <div class="mb-2 mr-4 ml-4">
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

        <CollectionBar />
        <PlaylistDrawer />
      </v-col>
    </v-row>

    <v-row v-if="collection == undefined || collection.length <= 0">
      <v-col>
        <v-progress-circular
          indeterminate
          color="primary"
        ></v-progress-circular>
      </v-col>
    </v-row>

    <v-row v-else>
      <CardCol v-for="item in collection" :key="item.id">
        <v-lazy :options="{ threshold: 0.5 }" transition="fade-transition">
          <YoutubeCard
            :item="item"
            :guid="item.guid"
            :title="item.title"
            :mediaId="item.media_id"
            :videoId="item.index"
            :thumbnail="item.thumbnail"
            :collected="item.collected"
          ></YoutubeCard>
        </v-lazy>
      </CardCol>
    </v-row>

    <BottomBar />
  </v-container>
</template>

<script>
import { mapGetters } from 'vuex'

import { mdiPlaylistMusic } from '@mdi/js'
import CardCol from '@/components/CardCol'
import BottomBar from '@/components/BottomBar'
import PlaylistDrawer from '@/components/PlaylistDrawer'
import YoutubeCard from '@/components/YoutubeCard/YoutubeCard'
import CollectionBar from '@/components/Collection/CollectionBar'

export default {
  name: 'CollectionView',
  components: {
    CardCol,
    BottomBar,
    YoutubeCard,
    CollectionBar,
    PlaylistDrawer,
  },
  computed: {
    ...mapGetters({
      guidIndex: 'collection/guidIndex',
      collection: 'collection/collectionSearchResults',
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
  mounted() {
    if (this.guidIndex.length >= 1) {
      this.$store.dispatch('collection/updateGuidIndex')
    }
  },
}
</script>
