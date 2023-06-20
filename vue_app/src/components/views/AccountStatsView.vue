<template>
    <div class="d-flex justify-center flex-column mx-8">

        <div class="d-flex">
            <LineChart class="w-50 mr-4" v-if="stats.play_count_history" title="Plays by Month" series-name="Plays" :chart-data="stats.play_count_history" />
            <LineChart class="w-50" v-if="stats.collection_count_history" title="Collected Items by Month" series-name="Items Collected" :chart-data="stats.collection_count_history" />
        </div>
        
        <!-- Most Played Track -->
        <div class="mb-1">
            <h3>Most Played Tracks</h3>
        </div>
        <v-simple-table>
            <template v-slot:default>
                <thead>
                    <tr>
                        <th class="text-left">
                            Name
                        </th>
                        <th class="text-left">
                            Plays
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in stats.top_ten_tracks" :key="item.media_id">
                        <td>{{ item.title }}</td>
                        <td>{{ item.plays }}</td>
                    </tr>
                </tbody>
            </template>
        </v-simple-table>
    </div>
</template>

<script>
import { getUserStats } from '@/services/api/UserStatsService';
import PlayCountHistoryChart from '../Charts/LineChart.vue';
import LineChart from '../Charts/LineChart.vue';

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
        });
    }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
