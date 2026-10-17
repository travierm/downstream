<template>
  <v-app>
    <NavBar />

    <NavDrawer />
    <PlaylistDrawer />

    <v-main>
      <router-view></router-view>
    </v-main>

    <BottomBar v-if="canShowBottomBar" />
  </v-app>
</template>

<script>
import NavBar from '@/components/Shared/NavBar.vue'
import PlaylistDrawer from '@/components/PlaylistDrawer'
import NavDrawer from '@/components/Shared/NavDrawer.vue'
import BottomBar from '@/components/BottomBar'
import { fetchInitUserData } from '@/store/events'

export default {
  name: 'App',

  components: {
    BottomBar,
    NavBar,
    NavDrawer,
    PlaylistDrawer,
  },
  mounted() {
    if(!this.onNonAuthedRoute) {
      fetchInitUserData()
    }
  },
  computed: {
    canShowBottomBar() {
      return !this.onNonAuthedRoute
    },
    onNonAuthedRoute() {
      return !this.$route.meta.requiresAuth
    },
  },
}
</script>

<style>
@import './assets/css/app.css';
</style>
