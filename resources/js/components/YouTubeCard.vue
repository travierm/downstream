<template>
  <div :id="sessionId + '_card'" :class="{ card: true , 'border-info': false, 'mx-auto': true }">

    <div id="cardToolbar" class="card-block">
      <!-- ADMIN THINGS -->
      <!-- @DEBUG -->
      <div v-if="false" class="float-left">
        <p>SessionID=>{{ sessionId }}</p>
      </div>



      <div class="float-right">
        <router-link v-if="ownerId && ((ownerId == userId) || userIsAdmin)" class="btn btn-outline-danger" :to="'/media/edit/' + this.mediaId">Edit</router-link>
        <button v-if="globalQueued && showGlobalQueue"  @click="pushGlobalQueue" class="btn btn-primary"><i class="fa fa-share" aria-hidden="true"></i> Queued </button>
        <button v-if="!globalQueued && showGlobalQueue"  @click="pushGlobalQueue" class="btn btn-outline-primary"><i class="fa fa-share" aria-hidden="true"></i> Global Queue</button>

        <div v-if="mediaId" class="btn-group">
          <a v-if="mediaId" :href="/v/ + videoId" class="btn btn-outline-primary" aria-haspopup="true" aria-expanded="false">
            Direct Link
          </a>
        </div>
        <button v-if="!collected" @click="discover" class="btn btn-outline-success">Collect</button>
        <button v-if="collected" @click="toss" class="btn btn-success">Collected</button>
      </div>
    </div>

    <div v-if="!playing" :id="this.sessionId + '_media'" class="media-container">

      <img style="width:100%; height: 100%" @click="parentPlay" :id="this.sessionId + '_thumbnail'" class="img-fluid" :src="formatThumbnail" />
      <div class="col">
        <div class="col-sm-12">
          <p style="color:white;">{{ title }}</p>
        </div>
      </div>
    </div>
    <!-- YouTube Player -->
    <div class="video-instance embed-responsive" :id="sessionId"></div>
  </div>
</template>

<script>
    import $ from 'jquery';
    import SID from 'shortid';
    import YTPlayer from 'yt-player';
    import { generateElementId } from '../services/Utils';

    let Utils = window._utils;

    //Component Props
    const props = {
      sessionId: {
        type: String,
        default: () => { return generateElementId() }
      },
      spotifyId: String,
      ownerId: Number,
      mediaId: Number,
      videoId: String,
      title: String,
      shouldPlay: Boolean,
      collected: Boolean,
      thumbnail: String,
      globalQueued: {
        type: Boolean,
        default: false,
      },
      showGlobalQueue: {
        type: Boolean,
        default: false
      }
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
      userIsAdmin() {
        return this.$store.getters['user/isAdmin'];
      },
      userId() {
        return this.$store.state.user.id;
      },
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

        if(this.shouldPlay) {
          setTimeout(() => {
            this.$store.dispatch('player/play', this.sessionId)
          }, 3000);
        }
      },
      destroyed() {
        //clean up player
        //fixes issue with player not being attached to dom after search update
        this.player = false;
      },
      methods: {
        parentPlay() {
          this.$store.dispatch('player/play', this.sessionId)
        },

        /**
         * Register With Player
         * 
         * passes item info to player to we can do things like play next track
         */
        playerRegister() {          
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
            fullscreen: true,
            playsinline: true,
            height: $(`#${this.sessionId}_media`).height(),
            width: $(`#${this.sessionId}_media`).width()
          };

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

          gtag('event', 'play', {
            'event_category' : 'Media',
            'event_label' : this.videoId
          });

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
            //this.$store.dispatch('collection/update');
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
            this.playerDeregister();
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
        pushGlobalQueue() {
          const data = {
            mediaId:this.mediaId
          };
          axios.post('/api/global/push', data).then((response) => {
            this.showGlobalQueue = true;
            this.globalQueued = true;
          });
        },
        directLink() {
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
.center {
    display: block;
    margin-left: auto;
    margin-right: auto;
    width: 50%;
}

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