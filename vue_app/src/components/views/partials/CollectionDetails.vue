<template>
    <v-container fluid>
        <v-sheet color="grey darken-4" elevation="1">
            <v-row class="ml-2" justify="start"> 

                <v-col cols="auto">
                    <v-btn color="primary">Playlists</v-btn>
                    <v-btn class="ml-2" color="success">Following</v-btn>
                </v-col>

                <v-col cols="auto">
                    <div class="title">Collection Size: {{ collection.length }}</div>
                </v-col>

                <v-col cols="auto">
                    <div class="title">Playlists: {{ 0 }}</div>
                </v-col>
            </v-row>
        </v-sheet>
    </v-container>
</template>

<script>
import { mapState } from "vuex"

export default {
    name: "CollectionDetails",
    components: {},
    computed: {
        ...mapState({
            collection: (state) => state.collection.collection,
        }),
    },
    watch: {},
    methods: {},
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
