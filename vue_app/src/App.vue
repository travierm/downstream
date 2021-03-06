<template>
  <v-app>
    <NavBar />

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
import PlaylistDrawer from './components/PlaylistDrawer'

export default {
  name: 'App',

  components: {
    NavBar,
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
