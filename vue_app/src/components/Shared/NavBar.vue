<template>
  <v-app-bar app color="dark" dark elevate-on-scroll>
    <div
      class="d-flex align-center"
      v-if="$vuetify.breakpoint.smAndUp || !loggedIn"
    >
      <router-link tag="div" to="/">
        <v-toolbar-title class="navbar-brand">Downstream</v-toolbar-title>
      </router-link>

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

    <v-spacer v-if="$vuetify.breakpoint.smAndUp || !loggedIn" />

    <!-- <template
      v-if="$vuetify.breakpoint.smAndUp && loggedIn && mediaStats.plays"
    >
      <v-toolbar-title>Plays</v-toolbar-title>
      <v-chip class="ma-2" color="secondary" text-color="white">
        Today: {{ mediaStats.plays.today }}
      </v-chip>

      <v-chip class="ma-2" color="secondary" text-color="white">
        Week: {{ mediaStats.plays.week }}
      </v-chip>

      <v-chip class="ma-2 mr-4" color="secondary" text-color="white">
        Month: {{ mediaStats.plays.month }}
      </v-chip>
    </template> -->

    <v-btn to="/login" outlined v-if="!loggedIn">Login</v-btn>

    <SearchBar v-if="loggedIn" class="mt-1" />

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
      <v-btn
        v-if="$vuetify.breakpoint.smAndUp && loggedIn"
        class="ml-4 mr-2"
        to="/collection"
        rounded
        text
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

    <v-menu left bottom v-if="$vuetify.breakpoint.xsOnly && loggedIn">
      <template v-slot:activator="{ on, attrs }">
        <v-btn icon v-bind="attrs" v-on="on">
          <v-icon>mdi-dots-vertical</v-icon>
        </v-btn>
      </template>

      <v-list>
        <v-list-item
          v-for="link in mobileLinks"
          :key="link.url"
          @click="$router.push(link.url)"
        >
          <v-list-item-title>{{ link.text }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>

    <v-menu offset-y v-if="$vuetify.breakpoint.smAndUp && loggedIn">
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
import { mapState, mapGetters } from 'vuex'
import SearchBar from '@/components/SearchBar'

const mobileLinks = [
  {
    text: 'Collection',
    url: '/collection',
  },
  {
    text: 'Logout',
    url: '/logout',
  },
]

export default {
  name: 'NavBar',
  components: {
    SearchBar,
  },
  computed: {
    ...mapState(['showLoadingBar', 'mediaStats']),
    ...mapGetters({
      loggedIn: 'auth/loggedIn',
    }),
  },
  data: () => {
    return {
      mobileLinks,
    }
  },
  mounted() {},
  methods: {},
}
</script>

<style scoped>
.navbar-brand {
  font-size: 2em;
  font-weight: 400;
  font-family: Roboto;
}

.main-search {
  padding-top: 22px !important;
}
</style>
