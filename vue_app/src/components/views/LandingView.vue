<template>
    <v-container fluid class="landingViewContent">
        <v-row class="justify-center">
            <v-col cols="12">
                <h2>Welcome to downstream</h2>

                <h3 class="mt-2">This site is currently under development.</h3>

                <v-btn to="/login" class="mt-4 mb-4" x-large outlined>
                    Login
                </v-btn>

                <YoutubeCard
                    guid="landing_1"
                    videoId="lZcRSy0sk5w"
                    title="Kid Cudi - Tequila Shots "
                    thumbnail="https://i.ytimg.com/vi/lZcRSy0sk5w/sddefault.jpg"
                    :hide-actions="true"
                ></YoutubeCard>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import YoutubeCard from "@/components/YoutubeCard/YoutubeCard"

export default {
    name: "LandingView",
    components: {
        YoutubeCard,
    },
    data: () => {
        return {}
    },
    mounted() {},
    methods: {},
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.landingViewContent {
    text-align: center;
}
</style>
