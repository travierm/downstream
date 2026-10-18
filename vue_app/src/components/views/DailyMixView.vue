<template>
  <v-container fluid>
    <div class="ml-1">
      <h1>Daily Mix</h1>
      <p>Recommendations based on music you collected in the last 30 days. Your mix refreshes every 24 hours.</p>
    </div>
    <v-row v-if="loading">
      <v-col>
        <h3 style="color: #1de9b6" role="status">Finding tracks for your Daily Mix...</h3>
      </v-col>
    </v-row>
    <v-alert v-else-if="errorMessage" type="error" variant="outlined" role="alert">
      {{ errorMessage }}
      <v-btn variant="text" @click="loadDailyMix">Try again</v-btn>
    </v-alert>
    <v-row v-else-if="results.length">
      <CardCol v-for="video in results" :key="video.guid">
        <YoutubeCard :item="video" :guid="video.guid" :title="video.title" :mediaId="video.mediaId"
          :videoId="video.videoId" :collected="video.collected" :thumbnail="video.thumbnail" />
      </CardCol>
    </v-row>
    <div v-else>
      <p role="status">{{ emptyMessage }}</p>
      <v-btn to="/collection" variant="text">Go to collection</v-btn>
      <v-btn variant="text" @click="loadDailyMix">Try again</v-btn>
    </div>
  </v-container>
</template>

<script>
import CardCol from '@/components/CardCol'
import YoutubeCard from '@/components/YoutubeCard/YoutubeCard'
import DiscoverService from '@/services/api/DiscoverService'

export default {
  name: 'DailyMixView',
  components: { CardCol, YoutubeCard },
  data: () => ({
    results: [],
    loading: false,
    errorMessage: false,
    emptyMessage: 'Add some tracks to your collection to get your Daily Mix.',
  }),
  mounted() {
    this.loadDailyMix()
  },
  methods: {
    async loadDailyMix() {
      if (this.loading) return
      this.loading = true
      this.errorMessage = false
      try {
        this.$store.dispatch('setLoadingBarState', true)
        const response = await DiscoverService.getDailyMix()
        this.results = response.data.videos || []
        this.emptyMessage = response.data.message || 'Add some tracks to your collection to get your Daily Mix.'
        await this.$store.dispatch('player/updateGuidData', {
          guidIndexKey: '/daily-mix',
          mediaItems: this.results,
        })
      } catch (error) {
        this.errorMessage = error?.data?.message || 'Could not load your Daily Mix. Please try again.'
      } finally {
        this.loading = false
        this.$store.dispatch('setLoadingBarState', false)
      }
    },
  },
}
</script>
