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

    <BottomBar />
  </v-app>
</template>

<script>
import { fetchInitUserData } from '@/store/events'
import NavBar from '@/components/Shared/NavBar.vue'
import PlaylistDrawer from '@/components/PlaylistDrawer'
import NavDrawer from '@/components/Shared/NavDrawer.vue'
import BottomBar from "@/components/BottomBar"

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
  data: () => ({
    //
  }),
}
</script>
