<template>
    <v-autocomplete
        id="searchInput"
        class="main-search"
        solo
        dense
        flat
        clearable
        hide-no-data
        append-icon=""
        :items="items"
        v-model="value"
        :search-input.sync="query"
        @input="dispatchSearchRoute()"
        @keyup.enter="dispatchSearchRoute()"
    >
    </v-autocomplete>
</template>

<script>
import $ from "jquery"
import { mapState } from "vuex"
import { getAutocompleteResults } from "../services/api/SearchService"

export default {
    name: "SearchBar",
    data: function() {
        const routeQuery = this.$route.query.query
        let items = routeQuery ? [routeQuery] : []

        return {
            items: items,
            value: routeQuery,
            query: "",
        }
    },
    methods: {
        dispatchSearchRoute() {
            console.log(this.query)
            const route = {
                path: "/search",
                query: {
                    query: (this.value ? this.value :this.query)
                },
            }

            this.$router.push(route)
        },
    },
    watch: {
        $route(to, from) {
            if (to.path == "/search" && to.query.query !== this.value) {
                this.items = [to.query.query]
                this.value = to.query.query
            }

            if (to.path !== "/search") {
                this.value = ""
            }
        },
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
