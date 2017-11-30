<template>
  <div class="container-fluid pushFromTop">
    <div class="row">
      <div class="col-lg-3"  v-for="video in videos" :key="video.id">
        <youtube-player-card :media-id="video.id" :vid="video.index" :collected="video.collected"></youtube-player-card>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapGetters, mapActions } from 'vuex'

  export default {
    mounted() {
      this.$store.dispatch('frontpage/update');
    },
    computed: {
      videos() {
        return this.$store.state.frontpage.videos;
      }
    }
  }
</script>

<style>
</style>
