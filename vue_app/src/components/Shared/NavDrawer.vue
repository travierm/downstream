<template>
  <v-navigation-drawer absolute right class="ds-nav-drawer mt-16" v-if="loggedIn && navDrawerStatus"
    v-click-outside="clickedOutOfNavDrawer">
    <!-- ^ height is 100vh - (top nav + bottom bar) -->
    <template v-slot:prepend>
      <v-list-item two-line>
        <v-list-item-avatar>
          <div class="userIcon" v-html="userIcon"></div>
        </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title>{{ user.display_name }}</v-list-item-title>
          <div class="d-flex justify-space-between">
            <span>
              <small> Logged In </small>
            </span>

            <span>
              <v-btn text x-small to="/logout"> Logout </v-btn>
            </span>
          </div>
        </v-list-item-content>
      </v-list-item>
    </template>

    <v-divider></v-divider>

    <v-list dense class="pt-0">
      <v-list-item v-for="item in navLinks" :key="item.title" class="ds-nav-drawer-item"
        @click="handleNavClick(item.to)">
        <v-divider v-if="item.title === 'divider'"></v-divider>
        <template v-else>
          <v-list-item-icon>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title>{{ item.title }}</v-list-item-title>
          </v-list-item-content>
        </template>
      </v-list-item>

      <v-list-item style="position: absolute; bottom: 0;">
        <span>
          <small>App Version: {{ getAppVersion() }}</small>
        </span>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>

<script>
import { mapState, mapGetters } from 'vuex'
import { identicon } from 'minidenticons'
import { getAppVersion } from '@/services/GlobalFunctions'

export default {
  name: 'NavDrawer',
  props: [],
  components: {},
  computed: {
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
        { icon: 'mdi-database', title: 'Collection', to: '/collection' },
        {
          icon: 'mdi-chart-bar-stacked',
          title: 'Stats',
          to: `/stats`,
        },
        { icon: 'mdi-spotify', title: 'Spotify Sync', to: '/spotify' },

        {
          title: 'divider',
        },
        {
          icon: 'mdi-star-circle',
          title: 'Top Users',
          to: `/users`
        },
        {
          icon: 'mdi-bell-alert-outline',
          title: 'Follower Feed',
          to: '/follower/activity'
        },
        // {
        //   icon: 'mdi-account-details',
        //   title: 'Your Profile',
        //   to: `/profile/${this.user.hash}`,
        // },
        {
          icon: 'mdi-account-multiple-plus',
          title: 'Invite Friends',
          to: `/invite`,
        },
      ]
    },
  },
  data() {
    return {}
  },
  directives: {
    'click-outside': {
      bind(el, binding, vnode) {
        el.clickOutsideEvent = (event) => {
          if (!(el == event.target || el.contains(event.target))) {
            vnode.context[binding.expression](event)
          }
        }

        document.body.addEventListener('click', el.clickOutsideEvent)
      },
      unbind(el) {
        document.body.removeEventListener('click', el.clickOutsideEvent)
      },
    },
  },
  methods: {
    getAppVersion,
    handleNavClick(path) {
      this.$router.push(path)
      this.$store.dispatch('toggleNavDrawerStatus')
    },
    clickedOutOfNavDrawer() {
      this.$store.dispatch('toggleNavDrawerStatus')
    },
  },
  watch: {},
}
</script>

<style lang="scss">
.ds-nav-drawer {
  height: calc(100vh - 112px) !important;
  background: #1e1e1e !important;
  position: fixed;
  z-index: 999;

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
