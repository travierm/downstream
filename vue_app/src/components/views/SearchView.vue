<template>
    <v-container fluid>
        <v-row v-if="results.length >= 1">
            <CardCol v-for="(video, index) in results" :key="index">
                <YoutubeCard
                    :item="video"
                    :guid="video.guid"
                    :title="video.title"
                    :mediaId="video.mediaId"
                    :videoId="video.videoId"
                    :collected="video.collected"
                    :thumbnail="video.thumbnail"
                ></YoutubeCard>
            </CardCol>
        </v-row>

        <BottomBar />
    </v-container>
</template>

<script>
import CardCol from "@/components/CardCol"
import YoutubeCard from "@/components/YoutubeCard/YoutubeCard"
import BottomBar from '@/components/BottomBar';

export default {
    name: "SearchView",
    components: {
        CardCol,
        BottomBar,
        YoutubeCard
    },
    data: () => ({
        results: [],
    }),
    computed: {
        searchQuery() {
            return this.$route.query.query
        }
    },
    mounted() {
        this.searchByQuery()
    },
    methods: {
        searchByQuery() {
            const query = this.searchQuery

            if(!query) {
                return
            }

            this.$store.dispatch('setLoadingBarState', true)
            this.$store
                .dispatch("search/byQuery", query)
                .then((response) => {
                    this.$store.dispatch('setLoadingBarState', false)

                    if (response.data.results) {
                        this.results = response.data.results
                        
                        const guidIndex =  this.results.map((item) => {
                            return item.guid
                        })

                        this.$store.dispatch('player/setGuidIndex', guidIndex)
                    }
                })
                .catch(() => {
                    this.$store.dispatch('setLoadingBarState', false)
                })
        },
    },
    watch: {
        searchQuery() {
            this.searchByQuery()
        }
    }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
