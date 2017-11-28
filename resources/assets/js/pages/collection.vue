<template>
  <div class="container-fluid pushFromTop">
    <div class="row">
      <div class="col-lg-6"  v-for="video in videos">
        <youtube-player-card v-bind:title="video.index" v-bind:vid="video.index" v-bind:media-id="video.id" collected="false"></youtube-player-card>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapGetters, mapActions } from 'vuex'

  export default {
    mounted() {
      this.$store.dispatch('collection/update');
    },
    data() {
      return {
        videos:this.$store.state.collection.videos
      }
    }
  }
</script>

<style>
</style>
