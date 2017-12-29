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
      <div class="col-lg-6" v-if="getVideo()" key="video.id">
        <youtube-player-card show-queue-controls="true" autoplay="true" :meta="getVideo().meta" :title="getVideo().title" height="500" :vid="getVideo().index" v-bind:media-id="getVideo().id" :collected="getVideo().collected"></youtube-player-card>
      </div>
      <div class="col-lg-3">

      </div>
    </div>

    <div style="display:body;" class="row justify-content-center">
      <div class="col-lg-6">
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
    created() {
      this.video = this.getVideo(this.currentMediaId);
    },
    methods: {
      getVideo() {
        const mediaId = this.currentMediaId;
        let videos = this.videos.filter((video) => {
          return video.id == mediaId;
        })

        if(videos.length < 1) {
          return false;
        }

        return videos[0];
      }
    },
    computed: {
      videos: {
        cache: false,
        get() {
          return this.$store.getters['media/queue'];
        }
      }
    }
  }
</script>

<style>
</style>
