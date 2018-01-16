<template>
  <div class="card">

    <!-- Editor -->
    <div v-if="canEdit" class="card-header">
      <button class="float-right btn btn-sm btn-outline-danger">Remove</button>
    </div>

    <div id="cardToolbar" class="card-block">
      <img v-if="playing == false" @click="play" height="30" width="30" src="/open-iconic-master/svg/media-play.svg" />
      <img class="media-icon" v-if="playing == true" @click="pause" height="30" width="30" src="/open-iconic-master/svg/media-pause.svg" />

      <div class="float-right">
        <button v-if="!videoCollected" @click="discover" class="btn btn-outline-success">Collect</button>
        <button v-if="videoCollected" @click="toss" class="btn btn-success">Collected</button>
      </div>
    </div>

    <!-- YouTube Player -->
    <div :id="this.id"></div>
    <img class="img-fluid" width="this.width" height="this.height" v-if="this.thumbnail && this.showThumbnail" v-bind:src="thumbnail" />

    <div class="card-block" v-if="title && meta ">
      <h4 class="card-title">{{title}}</h4>
      
      <h6 class="card-subtitle mb-2">Views - <span class="text-success">{{numberWithCommas(meta.view_count)}}</span></h6>
    </div>
  </div>
</template>

<script>
    import SID from 'shortid';

    export default {
      data() {
        return {
          id: SID.generate(),
          player: false,
          playing: false,
          isCollected: this.collected,
          lazyLoad: false,
          showThumbnail: true
        };
      },
      props: {
        autoplay: {
          required: false,
          default: false,
        },
        showQueueControls: {
          required: false,
          default: false
        },
        mediaId: {
          required: false,
          default: false,
        },
        vid: {
          type: String,
          required: true,
        },
        // most song will have been imported before being displayed
        imported: {
          type: Boolean,
          default: true,
        },
        title: {
          type: String,
          required: false,
        },
        thumbnail: {
          type: String,
          required: false
        },
        description: {
          type: String,
          required: false,
        },
        showText: {
          type: Boolean,
          required: false,
          default: true,
        },
        collected: {
          required: true,
          default: false,
        },
        meta: {
          default: false
        },
        height: {
          required: false,
          default: false,
        },
        width: {
          required: false,
          default: false,
        },
        canEdit: {
          type: Boolean,
          required: false,
          default: false,
        },
      },
      computed: {
        videoCollected() {
          return this.isCollected;
        },
      },
      mounted() {

        if(this.thumbnail) {
          this.showThumbnail = true;
          this.lazyLoad = true;
        }

        let self = this;

        if(!this.lazyLoad) {
          this.registerVideo();
        }

        console.log(this.height);
       
      },
      beforeDestroy() {
        this.player.destroy();
        this.$store.dispatch('video/destroy', {
          id:this.mediaId
        });
      },
      methods: {
        registerVideo() {
          const options = {};

          if (this.height) {
            options.height = this.height;
          }
          if (this.width) {
            options.width = this.width;
          } else {
            options.width = $(`#${this.id}`).width();
          }
          options.fullscreen = false;
          options.width -= 2;
          this.$store.dispatch('video/register', {
            id: this.mediaId,
            vid: this.vid,
            element: this.id,
            options: options
          });

          this.$store.dispatch('video/registerEventAction', {
            id: this.mediaId,
            eventType: 'playing',
            callback:() => {
              self.playing = true;
            }
          });

          this.$store.dispatch('video/registerEventAction', {
            id: this.mediaId,
            eventType: ['ended', 'paused'],
            callback:() => {
              self.playing = false;
            }
          });
        },
        numberWithCommas(x) {
          return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },
        play() {
          this.playing = true;
          if(this.lazyLoad) {
            this.showThumbnail = false;
            this.registerVideo();
          }
          this.$store.dispatch('video/play', this.mediaId);
        },
        pause() {
          this.playing = false;
          this.$store.dispatch('video/pause', this.mediaId);
        },
        collect() {

        },
        discover() {
          this.$store.dispatch('collection/discover', {
            type: 'youtube',
            videoId: this.vid,
          });
          this.isCollected = true;
        },
        toss() {
          if (!this.mediaId) {
            return false;
          }

          this.$store.dispatch('collection/toss', {
            type: 'youtube',
            mediaId: this.mediaId,
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
</style>
