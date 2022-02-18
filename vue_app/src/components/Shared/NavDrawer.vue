<template>
  <v-navigation-drawer
    absolute
    permanent
    right
    class="ds-nav-drawer mt-16"
    style="height: calc(100vh - 112px)"
    v-if="loggedIn && navDrawerStatus"
    v-click-outside="clickedOutOfNavDrawer"
  >
    <!-- ^ height is 100vh - (top nav + bottom bar) -->
    <template v-slot:prepend>
      <v-list-item two-line>
        <v-list-item-avatar>
          <v-icon>mdi-account</v-icon>
        </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title>{{ user.display_name }}</v-list-item-title>
          <div class="d-flex justify-space-between">
            <span>
              <small>
                Logged In
              </small>
            </span>

            <span>
              <v-btn text x-small to="/logout">
                Logout
              </v-btn>
            </span>
          </div>
        </v-list-item-content>
      </v-list-item>
    </template>

    <v-divider></v-divider>

    <v-list dense class="pt-0">
      <v-list-item
        v-for="item in navLinks"
        :key="item.title"
        class="ds-nav-drawer-item"
        :to="item.to"
      >
        <v-list-item-icon>
          <v-icon>{{ item.icon }}</v-icon>
        </v-list-item-icon>

        <v-list-item-content>
          <v-list-item-title>{{ item.title }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>

<script>
import { mapState, mapGetters } from 'vuex'

export default {
  name: 'NavDrawer',
  props: [],
  components: {},
  computed: {
    ...mapState({
      navDrawerStatus: state => state.navDrawerStatus,
      user: state => state.auth.user,
    }),
    ...mapGetters({
      loggedIn: 'auth/loggedIn',
    }),
  },
  data() {
    return {
      navLinks: [
        { icon: 'mdi-album', title: 'Collection', to: '/collection' },
        // { icon: 'mdi-account-multiple', title: 'Following', to: '/following' },
        // {icon: 'mdi-account', title: 'Account', to: ''},
      ],
    }
  },
  directives: {
    "click-outside": {
      bind(el, binding, vnode) {
        el.clickOutsideEvent = (event) => {
          if (!(el == event.target || el.contains(event.target))) {
            vnode.context[binding.expression](event);
          }
        };

        document.body.addEventListener('click', el.clickOutsideEvent)
      },
      unbind(el) {
        document.body.removeEventListener('click', el.clickOutsideEvent)
      },
    }
  },
  methods: {
    clickedOutOfNavDrawer () {
      this.$store.dispatch('toggleNavDrawerStatus')
    }
  },
}
</script>

<style lang="scss">
.ds-nav-drawer {
  height: calc(100vh - 112px) !important;
  background: #1e1e1e !important;

  .ds-nav-drawer-item {
    &:hover {
      background: #363636;
    }
  }
}
</style>
