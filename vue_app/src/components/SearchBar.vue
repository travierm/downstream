<template>
    <v-autocomplete
        class="main-search"
        solo
        dense
        flat
        hide-no-data
        :items="items"
        :search-input.sync="query"
        @keydown.enter="dispatchSearchRoute()"
    >
    </v-autocomplete>
</template>

<script>
import $ from "jquery"
import { mapState } from "vuex"
import { getAutocompleteResults } from "../services/api/SearchService"

export default {
    name: "SearchBar",
    data: () => ({
        query: null,
        items: [],
    }),
    methods: {
        dispatchSearchRoute() {
            const route = {
                path: "/search",
                query: {
                    query: this.query,
                },
            }

            this.$router.push(route)
        },
    },
    watch: {
        query(val) {
            if (!val) {
                this.items = []
                return
            }

            getAutocompleteResults(val, (items) => {
                this.items = items
            })
        },
    },
}
</script>

<style scoped></style>
