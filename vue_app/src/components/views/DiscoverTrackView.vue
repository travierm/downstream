<template>
  <v-container fluid>
    <v-row v-if="errorMessage">
      <v-col>
        <h3>{{ errorMessage }}</h3>
      </v-col>
    </v-row>

    <v-row v-else>
      <CardCol v-for="item in items" :key="item.guid">
        <v-lazy :options="{ threshold: 0.5 }" transition="fade-transition">
          <YoutubeCard
            :item="item"
            :guid="item.guid"
            :title="item.title"
            :mediaId="item.media_id"
            :videoId="item.videoId"
            :thumbnail="item.thumbnail"
            :collected="item.collected"
          ></YoutubeCard>
        </v-lazy>
      </CardCol>
    </v-row>
  </v-container>
</template>

<script>
import { mapGetters, mapState } from 'vuex'
import CardCol from '@/components/CardCol'
import YoutubeCard from '@/components/YoutubeCard/YoutubeCard'

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
  },
  components: {
    CardCol,
    YoutubeCard,
  },
  data: () => {
    return {}
  },
  mounted() {
    this.$store.dispatch(
      'discover/getSimilarTracks',
      this.$route.params.videoId
    )
  },
  methods: {},
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
