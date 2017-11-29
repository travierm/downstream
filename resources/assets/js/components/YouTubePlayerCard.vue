<template>
  <div id="card1" class="card">

    <!-- Editor -->
    <div v-if="canEdit" class="card-header">
      <button class="float-right btn btn-sm btn-outline-danger">Remove</button>
    </div>

    <div id="cardToolbar" class="card-block">
      <img v-if="playing == false" @click="play" height="30" width="30" src="/open-iconic-master/svg/media-play.svg" />
      <img v-if="playing == true" @click="pause" height="30" width="30" src="/open-iconic-master/svg/media-pause.svg" />

      <div class="float-right">
        <button v-if="!isCollected" @click="discover" class="btn btn-outline-success">Collect</button>
        <button v-if="isCollected" @click="toss" class="btn btn-success">Collected</button>
      </div>
    </div>

    <!-- YouTube Player -->
    <div :id="this.id"></div>
  </div>
</template>

<script>
    import YouTubePlayer from 'youtube-player';
    import SID from 'shortid';

    export default {
      data: () => ({
        id: SID.generate(),
        player: false,
        playing: false,
        isCollected: false,
    }),
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
          type: String,
          required: true,
          default: 'false',
        },
        canEdit: {
          type: Boolean,
          required: false,
          default: false,
        },
      },
      mounted() {
        if (this.collected) {
          this.isCollected = true;
        }

        const player = YouTubePlayer(this.id, {
          videoId: this.vid,
          width: $(`#${this.id}`).width(),
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
