<template>
  <div :id="sessionId + '_card'" :class="{ card: true , 'border-info': false, 'mx-auto': true }">

    <div id="cardToolbar" class="card-block">
      <!-- ADMIN THINGS -->
      <!-- @DEBUG -->
      <div v-if="false" class="float-left">
        <p>SessionID=>{{ sessionId }}</p>
      </div>



      <div class="float-right">
        <div v-if="mediaId" class="btn-group">
          <button v-on:click="directLink" type="button" class="btn btn-outline-primary" aria-haspopup="true" aria-expanded="false">
            Direct Link
          </button>
        </div>
        <button v-if="!collected" @click="discover" class="btn btn-outline-success">Collect</button>
        <button v-if="collected" @click="toss" class="btn btn-success">Collected</button>
      </div>
    </div>

    <div v-if="!playing" :id="this.sessionId + '_media'" class="media-container">

      <img  v-on:click="$store.dispatch('player/play', sessionId)" :id="this.sessionId + '_thumbnail'" class="img-fluid" :src="formatThumbnail" />
      <div class="col">
        <div class="col-sm-12">
          <p style="color:white;">{{ title }}</p>
        </div>
      </div>
    </div>
    <!-- YouTube Player -->
    <div class="video-instance border-success" :id="sessionId"></div>
  </div>
</template>

<script>
    import $ from 'jquery';
    import SID from 'shortid';
    import YTPlayer from 'yt-player';
    import { generateElementId } from '../services/Utils';

    let Utils = window._utils;
    window.ytp = YTPlayer;

    //Component Props
    const props = {
      sessionId: {
        type: String,
        default: () => { return generateElementId() }
      },
      spotifyId: String,
      mediaId: Number,
      videoId: String,
      title: String,
      shouldPlay: Boolean,
      collected: Boolean,
      thumbnail: String
    };

    let data = () => {
      return {
        playing: false,
        player: false,
        showThumbnail: true,
        showVideo: false
      }
    }

    const computed = {
      formatThumbnail() {
        if(this.hasBadThumbnail == true) {
          return "https://via.placeholder.com/640x480/000000?text=" + this.title;
        }

        return this.thumbnail;
      },
      hasBadThumbnail() {
        return (this.thumbnail.includes('/default.jpg'));
      },
    }

    export default {
      computed: computed,
      props: props,
      data: data,
      /**
       * @EVENT mounted
       */
      mounted() {
        this.playerRegister();
      },
      destroyed() {
        //this.playerDeregister();
      },
      methods: {
        /**
         * Register With Player
         * 
         * passes item info to player to we can do things like play next track
         */
        playerRegister() {
          if(!this.sessionId) {
            //this.sessionId = generateElementId();
          }

          this.$store.dispatch('player/register', {
            sessionId: this.sessionId,
            media: this.getMediaMeta(),
            callbackHandler: this.parentCallbackHandler
          })
        },
        playerDeregister() {
          this.$store.dispatch('player/deregister', {
            sessionId: this.sessionId,
            media: this.getMediaMeta(),
            callbackHandler: this.parentCallbackHandler
          })
        },
        /**
         * Load Video
         * doing loading of video so it can play
         */
        loadVideo() {
          const elementId = "#" + this.sessionId.replace(/["\\]/g, '\\$&');
          const options = {
            volume: 5,
            height: $(`#${this.sessionId}_media`).height(),
            width: $(`#${this.sessionId}_media`).width()
          };

          /*if(!document.querySelector(elementId)) {
            console.log("cant load " + sessionId + " because dom element is not attached");
            return;
          }*/

          let player = new YTPlayer($(elementId)[0], options);
          player.load(this.videoId);

          this.player = player;
        },
        /**
         * Play
         * triggered by clicking thumbnail
         */
        play(volume) {
          if(!this.player) {
            this.loadVideo();
          }
          
          this.player.setVolume(volume);
          this.player.play();

          this.playing = true;

          this.player.on('ended', () => {
            this.$store.dispatch('player/indexStepForward');
          })

          this.toggleThumbnail(false);
          this.toggleVideo(true);
        },
        pause() {
          this.player.pause();
          this.playing = false;

          this.toggleThumbnail(true);
          this.toggleVideo(false);
        },
        /**
        * Discover
        * add media to database if not already there
        * add to users collection
        */
        discover() {
          this.$store.dispatch('collection/discover', {
            type: 'youtube',
            videoId: this.videoId,
            spotifyId: this.spotifyId
          }).then((err, resp) => {
            this.collected = true;
            this.$store.dispatch('collection/update');
          }, (err) => {
            this.collected = false;
            $('#modals').show();
            this.$root.$emit('bv::show::modal','registerModal')
          });
        },
        /**
         * Toss
         * remove media item from collection but keep in database for others to collect
         */
        toss() {
          this.$store.dispatch('collection/toss', {
            type: 'youtube',
            mediaId: this.mediaId
          }).then((resp) => {
            this.$emit('tossed');
          });
          
          this.collected = false;
        },
        getMediaMeta() {
          return {
            title: this.title,
            mediaId: this.mediaId,
            videoId: this.videoId,
            thumbnail: this.thumbnail
          }
        },
        directLink() {
          console.log("CALLED");
          window.location.href = "/v/" + this.videoId;
        },
        parentCallbackHandler(callback) {
          let self = this;

          callback(self);
        },
        toggleThumbnail(bool) {
          if(bool) {
            $("#" + this.sessionId + "_thumbnail").show();
          }else{
            $("#" + this.sessionId + "_thumbnail").hide();
          }
        },
        toggleVideo(bool) {
          if(bool) {
            $("#" + this.sessionId).show();
          }else{
            $("#" + this.sessionId).hide();
          }
        }
      }
    }
</script>

<style scoped>
.media-icon {
  margin-right: 10px;
}
.card {
  margin-bottom: 20px;
}
#cardToolbar {
  margin: 15px 15px 15px 15px;
}

.media-container {
  position: relative;
} 
.media-container .col {
  position: absolute; 
  z-index: 1; 
  top: 0; 
  left: 0; 
  color: white; 
  margin-top: 10px;
}
</style>