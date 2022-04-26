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

      <template
        v-if="$vuetify.breakpoint.smAndUp && loggedIn && mediaStats.plays"
      >
        <v-toolbar-title class="ml-2 text-subtitle-1"
          >{{ mediaStats.plays.year }} videos played this year</v-toolbar-title
        >
      </template>
    </div>

    <v-spacer v-if="$vuetify.breakpoint.smAndUp || !loggedIn" />

    <v-btn to="/login" outlined v-if="!loggedIn">Login</v-btn>

    <SearchBar v-if="loggedIn" class="mt-1" />

    <v-app-bar-nav-icon
      v-if="loggedIn && $vuetify.breakpoint.smAndUp"
      @click.stop="$store.dispatch('toggleNavDrawerStatus')"
      class="ml-2"
    ></v-app-bar-nav-icon>

    <!-- Loading Bar -->
    <v-progress-linear
      :active="showLoadingBar"
      :indeterminate="showLoadingBar"
      absolute
      bottom
      color="deep-purple accent-4"
    ></v-progress-linear>
  </v-app-bar>
</template>

<script>
import { mapState, mapGetters } from 'vuex'
import SearchBar from '@/components/SearchBar'

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
    return {}
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
