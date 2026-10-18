<template>
  <v-app-bar color="app-bar" scroll-behavior="elevate">
    <div
      class="d-flex align-center pl-4"
      v-if="$vuetify.display.smAndUp || !loggedIn"
    >
      <router-link to="/" class="unstyled-link">
        <v-toolbar-title class="navbar-brand ds-gradient"
          >downstream</v-toolbar-title
        >
      </router-link>

      <v-btn
        href="https://github.com/travierm/downstream"
        target="_blank"
        variant="text"
        icon
        size="small"
      >
        <v-icon>$mdi-github</v-icon>
      </v-btn>

      <template
        v-if="$vuetify.display.smAndUp && loggedIn && mediaStats.plays"
      >
        <v-toolbar-title class="ml-2 text-subtitle-1"
          >{{ mediaStats.plays.year }} videos played this year</v-toolbar-title
        >
      </template>
    </div>

    <v-spacer v-if="$vuetify.display.smAndUp || !loggedIn" />

    <v-btn to="/login" variant="outlined" v-if="!loggedIn">Login</v-btn>

    <SearchBar v-if="loggedIn" class="mt-1" />

    <v-tooltip v-if="loggedIn && hasDailyMixCollection" location="bottom">
      <template #activator="{ props }">
        <v-btn v-bind="props" to="/daily-mix" icon variant="text" aria-label="Open Daily Mix">
          <v-icon>$mdi-music-note</v-icon>
        </v-btn>
      </template>
      <span>Daily Mix — recommendations from your recent collections</span>
    </v-tooltip>

    <v-app-bar-nav-icon
      v-if="loggedIn"
      aria-label="Open navigation menu"
      @click.stop="$store.dispatch('toggleNavDrawerStatus')"
      class="ml-2"
    ></v-app-bar-nav-icon>

    <!-- Loading Bar -->
    <v-progress-linear
      :active="showLoadingBar"
      :indeterminate="showLoadingBar"
      absolute
      location="bottom"
      color="deep-purple-accent-4"
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
    hasDailyMixCollection() {
      const cutoff = Date.now() - 30 * 24 * 60 * 60 * 1000
      return (this.collection || []).some((item) => {
        // Collection timestamps are returned as UTC SQL dates by the API.
        const date = item.created_at?.replace(' ', 'T')
        const collectedAt = Date.parse(date && !/(Z|[+-]\d{2}:?\d{2})$/.test(date) ? `${date}Z` : date)
        return !item.deleted_at && collectedAt >= cutoff
      })
    },
    ...mapState({
      collection: (state) => state.collection.collection,
    }),
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
