<template>
  <div class="container-fluid pushFromTop">
    <div class="row">
      <div class="col-lg-6 center" v-if="videos.length == 0">
        <h3>Frontpage is empty rn..</h3>
        <img height="250" width="450" src="https://media.giphy.com/media/hICCrVIACYZY4/giphy.gif" />
        <h4>Search & Collect to populate this page for others!</h4>
      </div>
    </div>

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
