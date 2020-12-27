<template>
    <v-container fluid>
        <v-row>
            <v-col>
            </v-col>
        </v-row>

        <v-row>
            <CardCol v-for="(video, index) in results" :key="index">
                <YoutubeCard
                    :item="video"
                    :title="video.title"
                    :videoId="video.videoId"
                    :thumbnailURL="video.thumbnail"
                ></YoutubeCard>
            </CardCol>
        </v-row>
    </v-container>
</template>

<script>
import CardCol from "@/components/CardCol"
import YoutubeCard from "@/components/YoutubeCard"

export default {
    name: "SearchView",
    components: {
        CardCol,
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

            this.$store
                .dispatch("search/byQuery", query)
                .then((response) => {
                    if (response.data.results) {
                        this.results = response.data.results
                    }
                })
                .catch(() => {})
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
