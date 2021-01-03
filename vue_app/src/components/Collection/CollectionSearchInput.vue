<template>
    <v-autocomplete
        color="secondary"
        v-model="value"
        :search-input.sync="query"
        :items="items"
        solo
        dense
        flat
        clearable
        label="Search Collection...."
        hide-details
        hide-selected
        light
        multiple
    ></v-autocomplete>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    name: "CollectionSearchInput",
    components: {},
    computed: {
        ...mapGetters({
            collection: "collection/collectionSearchResults",
        }),
    },
    data: () => {
        return {
            items: [],
            query: "",
            value: "",
        }
    },
    mounted() {},
    methods: {},
    watch: {
        query: function(val) {
            this.$store.commit('collection/SET_SEARCH_QUERY', val)

            let indexes = _.map(this.collectionSearchResults, "guid")
            this.$store.dispatch("player/setGuidIndex", indexes)
            console.log(indexes)
        },
    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
