<template>
  <v-container fluid>
    <v-row>
      <v-col>
        <h2 class="font-weight-bold">
          Discovered items of
          <span class="ml-1" style="color: #7c4dff">{{ media.title }}</span>
        </h2>
      </v-col>
    </v-row>

    <v-row v-if="errorMessage">
      <v-col lg="3">
        <v-alert dense outlined type="error">
          {{ errorMessage }}
        </v-alert>
      </v-col>
    </v-row>

    <v-row v-else>
      <CardCol v-for="item in items" :key="item.guid">
        <v-lazy :options="{ threshold: 0.5 }" transition="fade-transition">
          <YoutubeCard
            :item="item"
            :guid="item.guid"
            :title="item.title"
            :mediaId="item.mediaId"
            :videoId="item.videoId"
            :thumbnail="item.thumbnail"
            :collected="item.collected"
          ></YoutubeCard>
        </v-lazy>
      </CardCol>
    </v-row>

    <v-row v-if="!items && !errorMessage">
      <v-col>
        <h3 style="color: #1de9b6">Finding similar media...</h3>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapGetters, mapState } from 'vuex'
import CardCol from '@/components/CardCol'
import YoutubeCard from '@/components/YoutubeCard/YoutubeCard'
import { getMediaByVideoIndex } from '../../services/api/MediaService'

export default {
  name: 'DiscoverTrackView',
  computed: {
    ...mapState('discover', ['errorMessage']),
    ...mapGetters({
      similarTracks: 'discover/similarTracks',
    }),
    items() {
      return this.similarTracks(this.routeVideoId)
    },
    routeVideoId() {
      return this.$route.params.videoId
    },
    routeVideoTitle() {
      return this.$route.params.title
    },
  },
  components: {
    CardCol,
    YoutubeCard,
  },
  data: () => {
    return {
      media: {},
    }
  },
  mounted() {
    getMediaByVideoIndex(this.$route.params.videoId).then((response) => {
      this.media = response.data.item
    })

    this.$store.dispatch(
      'discover/getSimilarTracks',
      this.$route.params.videoId
    )
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
