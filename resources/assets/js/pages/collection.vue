<template>
  <div class="container-fluid pushFromTop">

    <div class="row" v-if="!isMobile">
      <div class="col-lg-2">
        <!-- <master-bar></master-bar> -->
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6 center" v-if="videos.length == 0">
        <h3>Nothing in collection..</h3>
        <img src="https://media.giphy.com/media/hEc4k5pN17GZq/giphy.gif" />
      </div>
    </div>

    <div class="row">
      <div class="col-lg-3" v-for="video in videos" :key="video.id">
        <video-player-card v-bind:media="video"></video-player-card>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    mounted() {
    },
    computed: {
      isMobile() {
        return window._isMobile;
      },
      videos() {
        return this.$store.getters['collection/videos'];
      },
    },
    methods: {
      gotoTheater() {
        const mediaId = this.videos[0].id;
        this.$router.push({
          name: 'theater',
          params: {
            mediaId,
          },
        });
      },
    },
  };
</script>

<style>
</style>
