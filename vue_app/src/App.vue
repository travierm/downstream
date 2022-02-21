<template>
  <v-app>
    <NavBar />

    <NavDrawer />
    <div class="position: absolute;">
      <PlaylistDrawer />
    </div>

    <v-main>
      <router-view></router-view>
    </v-main>

    <BottomBar v-if="canShowBottomBar" />
  </v-app>
</template>

<script>
import { mapState } from 'vuex'
import { fetchInitUserData } from '@/store/events'
import NavBar from '@/components/Shared/NavBar.vue'
import PlaylistDrawer from '@/components/PlaylistDrawer'
import NavDrawer from '@/components/Shared/NavDrawer.vue'
import BottomBar from '@/components/BottomBar'

export default {
  name: 'App',

  components: {
    BottomBar,
    NavBar,
    NavDrawer,
    PlaylistDrawer,
  },
  mounted() {
    fetchInitUserData()
  },
  computed: {
    canShowBottomBar() {
      return !this.authRoutes.includes(this.route)
    },
    ...mapState({
      route: (state) => state.route.path,
    }),
  },
  data: () => ({
    authRoutes: ['/', '/login', '/waitlist', '/register'],
  }),
}
</script>
