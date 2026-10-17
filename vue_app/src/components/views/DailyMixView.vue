<template>
  <v-container fluid>
    <div class="ml-1">
      <h1>Daily Mix</h1>
      <p>A curated list of songs based on your collection and listening history.</p>
    </div>
    <p v-if="loading" role="status">Finding tracks for your Daily Mix...</p>
    <v-alert v-else-if="errorMessage" type="error" outlined role="alert">
      {{ errorMessage }}
      <v-btn text @click="loadDailyMix">Try again</v-btn>
    </v-alert>
    <v-row v-else-if="results.length">
      <CardCol v-for="video in results" :key="video.guid">
        <YoutubeCard :item="video" :guid="video.guid" :title="video.title" :mediaId="video.mediaId"
          :videoId="video.videoId" :collected="video.collected" :thumbnail="video.thumbnail" />
      </CardCol>
    </v-row>
    <div v-else>
      <p role="status">{{ emptyMessage }}</p>
      <v-btn to="/collection" text>Go to collection</v-btn>
      <v-btn text @click="loadDailyMix">Try again</v-btn>
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
    emptyMessage: 'Collect or play some tracks to get your Daily Mix.',
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
        const response = await DiscoverService.getDailyMix()
        this.results = response.data.videos || []
        this.emptyMessage = response.data.message || 'Collect or play some tracks to get your Daily Mix.'
        await this.$store.dispatch('player/updateGuidData', {
          guidIndexKey: '/daily-mix',
          mediaItems: this.results,
        })
      } catch (error) {
        this.errorMessage = error?.data?.message || 'Could not load your Daily Mix. Please try again.'
      } finally {
        this.loading = false
      }
    },
  },
}
</script>
