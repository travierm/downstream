<template>
    <v-container fluid>
        <v-row>
            <v-col>
                <CollectionBar />
            </v-col>
        </v-row>

        <v-row v-if="collection == undefined || collection.length <= 0">
            <v-col>
                <v-progress-circular
                    indeterminate
                    color="primary"
                ></v-progress-circular>
            </v-col>
        </v-row>

        <v-row v-else>
            <CardCol v-for="item in collection" :key="item.id">
                <v-lazy
                    :options="{
                        // How many elements should be shown before loading it
                        threshold: 0.25,
                    }"
                    transition="fade-transition"
                >
                    <YoutubeCard
                        :item="item"
                        :guid="item.guid"
                        :title="item.title"
                        :mediaId="item.media_id"
                        :videoId="item.index"
                        :thumbnail="item.thumbnail"
                        :collected="item.collected"
                    ></YoutubeCard>
                </v-lazy>
            </CardCol>
        </v-row>

        <BottomBar />
    </v-container>
</template>

<script>
import { mapState, mapGetters } from "vuex"

import CardCol from "@/components/CardCol"
import BottomBar from "@/components/BottomBar"
import YoutubeCard from "@/components/YoutubeCard/YoutubeCard"
import CollectionBar from '@/components/Collection/CollectionBar'
import CollectionSearchInput from '@/components/Collection/CollectionSearchInput'

export default {
    name: "CollectionView",
    components: {
        CardCol,
        BottomBar,
        YoutubeCard,
        CollectionBar
    },
    computed: {
        ...mapGetters({
            collection: "collection/collectionSearchResults",
        }),
        collectionGuidIndex() {
            if (!this.collection) {
                return []
            }

            return this.collection.map((item) => {
                return item.guid
            })
        },
    },
    mounted() {
        if (this.collectionGuidIndex.length >= 1) {
            this.$store.dispatch(
                "player/setGuidIndex",
                this.collectionGuidIndex
            )
        }
    },
    watch: {
        collection(value) {
            if (this.collectionGuidIndex.length >= 1) {
                /*this.$store.dispatch(
                    "player/setGuidIndex",
                    this.collectionGuidIndex
                )*/
            }
        },
    },
    methods: {},
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
