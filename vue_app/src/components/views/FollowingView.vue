<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12">
        <h1>Active User's of Downstream</h1>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import BottomBar from '@/components/BottomBar'

export default {
  name: 'FollowingView',
  components: {
    BottomBar,
  },
  data: () => ({}),
  computed: {},
  methods: {},
  watch: {},
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.landingViewContent {
  text-align: center;
}
</style>
