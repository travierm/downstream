<template>
    <v-app-bar app color="dark" dark>
        <div class="d-flex align-center">
            <v-toolbar-title class="nav-title">downstream3</v-toolbar-title>

            <v-btn
                href="https://github.com/travierm/downstream"
                target="_blank"
                text
                fab
                small
            >
                <v-icon>mdi-github</v-icon>
            </v-btn>
        </div>

        <v-spacer />

        <SearchBar />

        <!-- Loading Bar -->
        <v-progress-linear
            :active="showLoadingBar"
            :indeterminate="showLoadingBar"
            absolute
            bottom
            color="deep-purple accent-4"
        ></v-progress-linear>

        <div v-if="loggedIn">
            <!-- Activity -->
            <!--<v-btn class="mr-2 ml-2" to="/activity" rounded text>
                Activity
                <v-badge
                    content="6"
                    class="ml-2 mr-2"
                    color="#1E1E1E"
                ></v-badge>
            </v-btn>-->

            <!-- Collection -->
            <v-btn class="ml-4 mr-2" to="/collection" rounded text
                >Collection</v-btn
            >

            <!-- Discover -->
            <!-- <v-btn class="mr-2 ml-2" rounded text>
                Discover
                <v-badge
                    content="2"
                    class="ml-2 mr-2"
                    color="#1E1E1E"
                ></v-badge>
            </v-btn> -->
        </div>

        <v-menu offset-y>
            <template v-slot:activator="{ on, attrs }">
                <v-btn v-bind="attrs" v-on="on" rounded text>
                    <v-icon>mdi-account</v-icon>
                </v-btn>
            </template>
            <v-list class="pa-2">
                <!--<v-list-item>
                    <v-icon class="pr-4">mdi-account-circle-outline</v-icon>
                    <v-list-item-title>Profile</v-list-item-title>
                </v-list-item>-->

                <!-- <v-list-item>
                    <v-icon class="pr-4">mdi-cog</v-icon>
                    <v-list-item-title>Settings</v-list-item-title>
                </v-list-item> -->

                <v-list-item>
                    <v-icon class="pr-4">mdi-logout</v-icon>
                    <v-list-item-title>
                        <router-link to="/logout">Logout</router-link>
                    </v-list-item-title>
                </v-list-item>
            </v-list>
        </v-menu>
    </v-app-bar>
</template>

<script>
import { mapState, mapGetters } from "vuex"
import SearchBar from "@/components/SearchBar"

export default {
    name: "NavBar",
    components: {
        SearchBar,
    },
    computed: {
        ...mapState(["showLoadingBar"]),
        ...mapGetters({
            loggedIn: "auth/loggedIn",
        }),
    },
    data: () => {
        return {}
    },
    mounted() {},
    methods: {},
}
</script>

<style scoped>
.nav-title {
    font-size: 2em;
}

.main-search {
    padding-top: 22px !important;
}
</style>
