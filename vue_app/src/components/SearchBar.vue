<template>
    <v-autocomplete
        id="searchInput"
        class="main-search"
        placeholder="Search for music..."
        variant="solo"
        density="compact"
        flat
        clearable
        hide-no-data
        append-inner-icon=""
        :items="items"
        v-model="value"
        v-model:search="query"
        @update:model-value="dispatchSearchRoute()"
        @keyup.enter="dispatchSearchRoute()"
    >
    </v-autocomplete>
</template>

<script>
import { getAutocompleteResults } from '../services/api/SearchService'

export default {
    name: 'SearchBar',
    computed: {
        currentRouteQuery() {
            return this.$route.query.query
        },
    },
    data: function() {
        const routeQuery = this.$route.query.query
        let items = routeQuery ? [routeQuery] : []

        return {
            items: items,
            value: routeQuery,
            query: '',
        }
    },
    methods: {
        getNextRouteQuery() {
            // Prefer value unless it matches the currentRouteQuery
            if (this.currentRouteQuery == this.value) {
                return this.query
            }

            return this.value
        },
        dispatchSearchRoute() {
            const route = {
                path: '/search',
                query: {
                    query: this.getNextRouteQuery(),
                },
            }

            this.$router.push(route)
        },
    },
    watch: {
        $route(to, from) {
            if (to.path == '/search' && to.query.query !== this.value) {
                this.items = [to.query.query]
                this.value = to.query.query
            }

            if (to.path !== '/search') {
                this.value = ''
            }
        },
        query(val) {
            if (!val) {
                this.items = []
                return
            }

            getAutocompleteResults(val, items => {
                this.items = items
            })
        },
    },
}
</script>
