
<template>
  <div class="container-fluid pushFromTop">

    <div class="row" v-if="!isMobile">
      <div class="col-lg-2">
        <master-bar></master-bar>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6 center" v-if="videos.length == 0">
        <h3>Nothing in collection..</h3>
        <img src="https://media.giphy.com/media/hEc4k5pN17GZq/giphy.gif" />
      </div>
    </div>

    <div class="row pushFromTop">
      <div class="col-lg-12">
        <h5>Things you can do</h5>
        <router-link class="btn btn-outline-danger" to="/search">Search for Music</router-link>
        <a href="/all" class="btn btn-outline-info">See what other people are collecting</a>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-12" v-for="video in videos" :key="video.id">
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
        return this.$store.getters['media/collection'];
      },
    },
    methods: {
    },
  };
</script>

<style>
</style>
