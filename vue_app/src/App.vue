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
  </v-app>
</template>

<script>
import { fetchInitUserData } from '@/store/events'
import NavBar from '@/components/Shared/NavBar.vue'
import PlaylistDrawer from '@/components/PlaylistDrawer'
import NavDrawer from '@/components/Shared/NavDrawer.vue'

export default {
  name: 'App',

  components: {
    NavBar,
    NavDrawer,
    PlaylistDrawer,
  },
  mounted() {
    fetchInitUserData()
  },
  data: () => ({
    //
  }),
}
</script>
