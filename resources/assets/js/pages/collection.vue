<template>
  <div class="container-fluid pushFromTop">
    <div class="row">
      <div class="col-lg-3" v-for="video in videos" :key="video.id">
        <youtube-player-card v-bind:title="video.index" v-bind:vid="video.index" v-bind:media-id="video.id" :collected="video.collected"></youtube-player-card>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    computed: {
      videos() {
        return this.$store.getters['collection/videos'];
      },
    },
  };
</script>

<style>
</style>
