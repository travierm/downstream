<template>
  <div class="card">

    <!-- Editor -->
    <div v-if="canEdit" class="card-header">
      <button class="float-right btn btn-sm btn-outline-danger">Remove</button>
    </div>

    <div id="cardToolbar" class="card-block">
      <img v-if="playing == false" @click="play" height="30" width="30" src="/open-iconic-master/svg/media-play.svg" />
      <img v-if="playing == true" @click="pause" height="30" width="30" src="/open-iconic-master/svg/media-pause.svg" />

      <div class="float-right">
        <button v-if="!videoCollected" @click="discover" class="btn btn-outline-success">Collect</button>
        <button v-if="videoCollected" @click="toss" class="btn btn-success">Collected</button>
      </div>
    </div>

    <!-- YouTube Player -->
    <div :id="this.id"></div>

    <div class="card-block" v-if="title && meta">
      <h4 class="card-title">{{title}}</h4>
      <h6 class="card-subtitle mb-2">Views - <span class="text-success">{{numberWithCommas(meta.view_count)}}</span></h6>
    </div>
  </div>
</template>

<script>
    import YouTubePlayer from 'youtube-player';
    import SID from 'shortid';

    export default {
      data() {
        return {
          id: SID.generate(),
          player: false,
          playing: false,
          isCollected: this.collected,
        };
      },
      props: {
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
        const options = {};
        if (this.height) {
          options.height = this.height;
        }
        if (this.width) {
          options.width = this.width;
        } else {
          options.width = $(`#${this.id}`).width();
        }

        const player = YouTubePlayer(this.id, {
          videoId: this.vid,
          ...options,
        });
        this.player = player;

        this.$store.dispatch('media/registerVideo', {
          id: this.id,
          player,
        });
      },
      beforeDestroy() {
        this.player.destroy();
        /* this.$store.dispatch('media/destroyVideo', {
          id:this.id
        }); */
      },
      methods: {
        numberWithCommas(x) {
          return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },
        play() {
          this.playing = true;
          this.$store.dispatch('media/playVideo', {
            id: this.id,
          });
        },
        pause() {
          this.playing = false;
          this.$store.dispatch('media/pauseVideo', {
            id: this.id,
          });
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
.card {
  margin-bottom: 20px;
}
#cardToolbar {
  margin: 15px 15px 15px 15px;
}
</style>
