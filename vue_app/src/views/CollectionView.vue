<template>
    <v-container fluid>
        <v-row v-if="userCollection == undefined || userCollection.length <= 0">
            <v-col>
                <v-progress-circular
                    indeterminate
                    color="primary"
                ></v-progress-circular>
            </v-col>
        </v-row>

        <v-row v-else>
            <CardCol v-for="item in userCollection" :key="item.id">
                <v-lazy
                    :options="{
                        // How many elements should be shown before loading it
                        threshold: 0.25,
                    }"
                    transition="fade-transition"
                >
                    <YoutubeCard
                        :item="item"
                        :videoId="item.index"
                        :thumbnailURL="item.meta.thumbnail"
                    ></YoutubeCard>
                </v-lazy>
            </CardCol>
        </v-row>
    </v-container>
</template>

<script>
import { mapState } from "vuex"
import CardCol from "@/components/CardCol"
import YoutubeCard from "@/components/YoutubeCard"

export default {
    name: "CollectionView",
    components: {
        CardCol,
        YoutubeCard
    },
    computed: {
        ...mapState({
            userCollection: (state) => state.collection.userCollection,
        }),
    },
    data: () => {
        return {}
    },
    mounted() {
        // this.$store.dispatch('collection/fetchUserCollection')
    },
    methods: {},
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
