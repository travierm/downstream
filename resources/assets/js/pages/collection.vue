<template>
  <div class="container-fluid pushFromTop">

    <div class="row" style="padding-bottom:20px;">
      <div class="col">
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
        <youtube-player-card v-bind:title="video.index" v-bind:vid="video.index" v-bind:media-id="video.id" :collected="video.collected"></youtube-player-card>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    mounted() {
    },
    computed: {
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
