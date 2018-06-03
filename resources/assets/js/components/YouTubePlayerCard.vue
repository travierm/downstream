<template>
  <div class="card">

    <div id="cardToolbar" class="card-block">
      <img class="media-icon" v-if="playing == false" @click="play" height="30" width="30" src="/open-iconic-master/svg/media-play.svg" />
      <img class="media-icon" v-if="playing == true" @click="pause" height="30" width="30" src="/open-iconic-master/svg/media-pause.svg" />

      <div class="float-right">
        <button v-if="!videoCollected" @click="discover" class="btn btn-outline-primary">Collect</button>
        <button v-if="videoCollected" @click="toss" class="btn btn-primary">Collected</button>
      </div>
    </div>

    <!-- YouTube Player -->
    <div :id="this.id"></div>
   

    <div class="card-block">
      <h4 class="card-title"></h4>
      
      <!-- <h6 class="card-subtitle mb-2">Views - <span class="text-success">{{Utils.numberWithCommas(media.meta.view_count)}}</span></h6> -->
    </div>
  </div>
</template>

<script>
    import SID from 'shortid';
    import YTPlayer from 'yt-player';

    let Utils = window._utils;


    export default {
      data() {
        return {
          id: SID.generate(),
          playing: false,
          isCollected: this.collected,
          lazyLoad: false,
          showThumbnail: true,
          Utils:Utils
        };
      },
      props: {
        autoplay: {
          required: false,
          default: false,
        },
        collected: {
          required: false,
          default: false
        },
        vid: {
          required: true,
          default: false
        }
      },
      computed: {
        videoCollected() {
          return this.isCollected;
        },
      },
      mounted() {

        
        this.registerVideo(this.vid);
      },
      beforeDestroy() {
      },
      methods: {
        updatePlayingState(playing) {
          if(playing) {
            this.playing = true;
            this.showThumbnail = false;
          }else{
            this.playing = false;
          }
        },
        registerVideo(vid, options = {}) {
          //options.height = $(`#${this.id}`).height();
          options.width = $(`#${this.id}`).width();

          /*if(options.height == 0) {
            options.height = $(`#${this.id}_thumbnail`).first().height();
          }*/

          const player = new YTPlayer("#" + this.id, options);
          player.load(this.vid);
        },
        play() {
          this.playing = true;
          this.showThumbnail = false;
          this.$store.dispatch('video/play', this.media.id);
        },
        pause() {
          this.playing = false;
          this.$store.dispatch('video/pause');
        },
        discover() {
          this.$store.dispatch('collection/discover', {
            type: 'youtube',
            videoId: this.vid,
          });
          this.isCollected = true;
        },
        toss() {
          if (!this.media.id) {
            return false;
          }

          this.$store.dispatch('collection/toss', {
            type: 'youtube',
            mediaId: this.media.mediaId,
          });
          this.isCollected = false;
        },
      },
    };
</script>

<style>
.media-icon {
  margin-right: 10px;
}
.card {
  margin-bottom: 20px;
}
#cardToolbar {
  margin: 15px 15px 15px 15px;
}

.media-container {position: relative;} 
.media-container .col {position: absolute; z-index: 1; top: 0; left: 0; color: white; margin-top: 10px;}
</style>
