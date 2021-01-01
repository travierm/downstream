<template>
  <v-app>
    <NavBar />

    <v-main>
      <router-view></router-view>
    </v-main>
  </v-app>
</template>

<script>
import { fetchInitUserData } from '@/store/events';
import NavBar from '@/components/Shared/NavBar.vue'

export default {
  name: 'App',

  components: {
    NavBar
  },
  mounted() {
    fetchInitUserData()
  },
  data: () => ({
    //
  }),
};
</script>
