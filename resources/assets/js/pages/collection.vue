<template>
  <div class="container-fluid pushFromTop">
    <div class="row">
        <div class="col-lg-3"  v-for="video in collection">
          <youtube-player-card v-bind:vid="video.vid" collected="false"></youtube-player-card>
        </div>
    </div>
  </div>
</template>

<script>
  import { mapGetters, mapActions } from 'vuex'

  export default {
    computed: mapGetters([
      'collection'
    ])
  }
</script>

<style>
</style>
