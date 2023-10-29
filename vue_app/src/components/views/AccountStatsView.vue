<template>
    <div class="d-flex justify-center flex-column mx-8 mt-2">
        <div class="d-flex ml-3">
            <LineChart class="w-50 mr-4" v-if="stats.play_count_history" title="Plays by Month" series-name="Plays"
                series-color="#F5F3C1" :chart-data="stats.play_count_history" />
            <LineChart class="w-50" series-color="#00FFCA" v-if="stats.collection_count_history" title="Collection Growth"
                series-name="Collection Size" :chart-data="stats.collection_count_history" :hide-bottom-labels="true" />
        </div>


        <div class="d-flex ml-3">
            <LineChart class="w-100" v-if="stats.play_count_history_by_days" title="Daily Activity" series-name="Plays"
                series-color="#05BFDB" :chart-data="stats.play_count_history_by_days" />
        </div>

        <!-- Most Played Track -->
        <div class="mb-1 ml-3">
            <h3>Most Played Tracks</h3>
        </div>
        <v-container fluid>
            <v-row v-if="stats.top_played_tracks">
                <CardCol v-for="item in stats.top_played_tracks" :key="item.guid">
                    <v-lazy :options="{ threshold: 0.5 }" transition="fade-transition">
                        <YoutubeCard :plays="item.plays" :item="item.media" :guid="item.media.guid"
                            :title="item.media.title" :mediaId="item.media.media_id" :videoId="item.media.index"
                            :spotifyId="item.media.spotify_id" :thumbnail="item.media.thumbnail" :collected="true"
                            :key="item.guid"></YoutubeCard>
                    </v-lazy>
                </CardCol>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { getUserStats } from '@/services/api/UserStatsService';
import PlayCountHistoryChart from '../Charts/LineChart.vue';
import LineChart from '../Charts/LineChart.vue';
import { getCurrentPathFromURL } from '@/services/GlobalFunctions'


export default {
    name: 'AccountStatsView',
    components: { PlayCountHistoryChart, LineChart },
    data: () => ({
        stats: {}
    }),
    computed: {},
    methods: {},
    watch: {},
    mounted() {
        getUserStats().then((response) => {
            this.stats = response.data;

            if (this.stats.top_played_tracks?.length >= 1) {
                this.$store.dispatch('player/updateGuidData', {
                    guidIndexKey: getCurrentPathFromURL(),
                    mediaItems: this.stats.top_played_tracks.map((item) => item.media),
                })
            }
        });
    }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
