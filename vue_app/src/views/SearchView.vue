<template>
    <v-container fluid>
        <v-row>
            <v-col>
                <h1>Search Page</h1>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
    name: "SearchView",
    components: {},
    data: () => {
        return {}
    },
    mounted() {
        this.searchByQuery()
    },
    methods: {
        searchByQuery() {
            const query = this.$route.query.query

            this.$store
                .dispatch("search/byQuery", query)
                .then((data) => {
                    console.log(data)
                })
                .catch(() => {})
        },
    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
