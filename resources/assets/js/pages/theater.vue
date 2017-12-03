<!--
Start with initial mediaId
Goto /api/theater to get a queue built
Play current media
Move to next media
-->
<template>
  <div class="container-fluid pushFromTop">
    <div style="display:body;" class="row justify-content-center">
      <div class="col-lg-3">

      </div>
      <div class="col-lg-6" v-if="video">
        <youtube-player-card :meta="video.meta" :title="video.meta.title" height="500" :vid="video.index" v-bind:media-id="video.id" :collected="video.collected"></youtube-player-card>
      </div>
      <div class="col-lg-3">

      </div>
    </div>
  </div>
</template>

<script>
  import { mapGetters, mapActions } from 'vuex'

  export default {
    data() {
      return {
        currentMediaId: this.$route.params.mediaId,
        player: false,
        playing: false
      };
    },
    mounted() {
      this.$store.dispatch('media/getTheaterQueue', this.currentMediaId);
    },
    computed: {
      video() {
        let self = this;
        let videos = this.videos.filter((video) => {
          return video.id !== self.currentMediaId;
        })
        if(videos.length <= 0) {
          return false;
        }

        let selected = videos[0];
        console.log(selected);
        selected.meta = JSON.parse(selected.meta);

        return selected;
      },
      videos() {
        return this.$store.state.frontpage.videos;
      }
    }
  }
</script>

<style>
</style>
