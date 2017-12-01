<template>
  <div class="container-fluid pushFromTop">
    <div class="row">
      <div class="col-lg-3" v-for="video in videos" :key="video.id">
        <youtube-player-card v-bind:title="video.index" v-bind:vid="video.index" v-bind:media-id="video.id" :collected="video.collected"></youtube-player-card>
      </div>
    </div>
    <input type="hidden" v-on:keyup.space="gotoTheater">
  </div>
</template>

<script>
  export default {
    mounted() {
      let self = this;
      document.body.onkeyup = function(e){
          if(e.keyCode == 32){
            console.log(self);
            self.gotoTheater();
          }
      }
    },
    computed: {
      videos() {
        return this.$store.getters['collection/videos'];
      },
    },
    methods: {
      gotoTheater() {
        console.log('going to theater');
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
