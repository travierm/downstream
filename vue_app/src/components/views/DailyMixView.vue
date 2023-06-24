<template>
    <v-container fluid>
        <div class="ml-1">
            <h1>Daily Mix</h1>
            <p>A curated list of songs based on what you've collected this week and songs you've listen to in the past.</p>
        </div>
        <v-row v-if="results.length >= 1">
            <CardCol v-for="(video, index) in results" :key="index">
                <YoutubeCard :item="video" :guid="video.guid" :title="video.title" :mediaId="video.mediaId"
                    :videoId="video.videoId" :collected="video.collected" :thumbnail="video.thumbnail"></YoutubeCard>
            </CardCol>
        </v-row>
    </v-container>
</template>

<script>
import CardCol from '@/components/CardCol'
import YoutubeCard from '@/components/YoutubeCard/YoutubeCard'
import { getCurrentPathFromURL } from '@/services/GlobalFunctions'
import DiscoverService from '@/services/api/DiscoverService'

export default {
    name: 'SearchView',
    components: {
        CardCol,
        BottomBar,
        YoutubeCard,
    },
    data: () => ({
        results: [],
    }),
    mounted() {
        DiscoverService.getDailyMix().then((response) => {
            this.results = response.data.videos
            console.log(this.results)

            this.$store.dispatch('player/updateGuidData', {
                guidIndexKey: getCurrentPathFromURL(),
                mediaItems: this.results
            })
        })


    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
