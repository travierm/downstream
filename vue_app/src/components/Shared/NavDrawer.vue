<template>
  <v-navigation-drawer
    v-if="loggedIn"
    v-model="drawerOpen"
    location="right"
    temporary
    class="ds-nav-drawer"
  >
    <template #prepend>
      <v-list-item lines="two">
        <template #prepend>
          <v-avatar><div class="userIcon" v-html="userIcon"></div></v-avatar>
        </template>
        <v-list-item-title>{{ user.display_name }}</v-list-item-title>
        <div class="d-flex justify-space-between align-center">
          <small>Logged In</small>
          <v-btn variant="text" size="x-small" to="/logout">Logout</v-btn>
        </div>
      </v-list-item>
    </template>

    <v-divider />
    <v-list density="compact" class="pt-0">
      <template v-for="item in navLinks" :key="item.title">
        <v-divider v-if="item.title === 'divider'" />
        <v-list-item v-else :prepend-icon="item.icon" class="ds-nav-drawer-item"
          @click="handleNavClick(item.to)">
          <v-list-item-title>{{ item.title }}</v-list-item-title>
        </v-list-item>
      </template>
    </v-list>
    <template #append>
      <div class="pa-4 text-caption">App Version: {{ getAppVersion() }}</div>
    </template>
  </v-navigation-drawer>
</template>

<script>
import { mapState, mapGetters } from 'vuex'
import { identicon } from 'minidenticons'
import { mdiViewDashboardOutline } from '@mdi/js'
import { getAppVersion } from '@/services/GlobalFunctions'

export default {
  name: 'NavDrawer',
  props: [],
  components: {},
  computed: {
    drawerOpen: {
      get() { return this.navDrawerStatus },
      set(value) { this.$store.commit('SET_NAV_DRAWER_STATUS', value) },
    },
    ...mapState({
      navDrawerStatus: (state) => state.navDrawerStatus,
      user: (state) => state.auth.user,
    }),
    ...mapGetters({
      loggedIn: 'auth/loggedIn',
    }),
    userIcon() {
      return identicon(this.user.display_name)
    },
    navLinks() {
      return [
        ...(this.user.type === 'admin'
          ? [{ icon: mdiViewDashboardOutline, title: 'Admin Panel', to: '/admin' }]
          : []),
        { icon: '$mdi-database', title: 'Collection', to: '/collection' },
        { icon: '$mdi-music-note', title: 'Daily Mix', to: '/daily-mix' },
        {
          icon: '$mdi-chart-bar-stacked',
          title: 'Stats',
          to: `/stats`,
        },
        { icon: '$mdi-spotify', title: 'Spotify Sync', to: '/spotify' },

        {
          title: 'divider',
        },
        {
          icon: '$mdi-star-circle',
          title: 'Top Users',
          to: `/users`
        },
        {
          icon: '$mdi-bell-alert-outline',
          title: 'Follower Feed',
          to: '/follower/activity'
        },
        // {
        //   icon: '$mdi-account-details',
        //   title: 'Your Profile',
        //   to: `/profile/${this.user.hash}`,
        // },
        {
          icon: '$mdi-account-multiple-plus',
          title: 'Invite Friends',
          to: `/invite`,
        },
      ]
    },
  },
  data() {
    return {}
  },
  methods: {
    getAppVersion,
    handleNavClick(path) {
      this.$router.push(path)
      this.drawerOpen = false
    },
  },
  watch: {},
}
</script>

<style lang="scss">
.ds-nav-drawer {
  background: #1e1e1e !important;

  .ds-nav-drawer-item {
    &:hover {
      background: #363636;
    }
  }

  .userIcon {
    width: 100%;
    height: 100%;
    background-color: #272727;
  }
}
</style>
