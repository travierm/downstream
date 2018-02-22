<template>
  <div class="card">

    <div id="cardToolbar" class="card-block">
      <img class="media-icon" v-if="playing == false" @click="play" height="30" width="30" src="/open-iconic-master/svg/media-play.svg" />
      <img class="media-icon" v-if="playing == true" @click="pause" height="30" width="30" src="/open-iconic-master/svg/media-pause.svg" />

      <div class="float-right">
        <router-link class="d-inline-flex p-2" :to="media.user.profileLink">User: <span class="text-success">{{ media.user.smallHash }}</span></router-link>
        <button v-if="!videoCollected" @click="discover" class="btn btn-outline-info">Collect</button>
        <button v-if="videoCollected" @click="toss" class="btn btn-info">Collected</button>
      </div>
    </div>

    <div :id="this.id + '_media'" class="media-container" v-if="media.meta.thumbnail && this.showThumbnail">
      <img @click="play" :id="this.id + '_thumbnail'" class="img-fluid" width="this.width" v-bind:src="media.meta.thumbnail" />
      <div class="col">
        <div class="col-sm-12">
          <p style="color:white;">{{ media.meta.title }} </p>
        </div>
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
    let Utils = window._utils;


    export default {
      data() {
        return {
          id: SID.generate(),
          playing: false,
          isCollected: (this.media.collected == true),
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
        media: {
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
       let self = this;
       //this.height = $('')
       this.registerVideo();

       this.$store.dispatch('video/registerEventAction', {
          id:this.media.id,
          eventType: ["buffering", "playing"],
          callback: () => {
            console.log("started playing");
            self.updatePlayingState(true);
          }
       })

       this.$store.dispatch('video/registerEventAction', {
          id:this.media.id,
          eventType: ["paused", "ended"],
          callback: () => {
            self.updatePlayingState(false);
          }
       })
      },
      beforeDestroy() {
        this.$store.dispatch('video/destroy', this.media.id);
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
        registerVideo(options = {}) {
          //options.height = $(`.img-fluid`).first().height();
          options.width = $(`#${this.id}`).width();

          console.log('video registered');

          this.$store.dispatch('video/register', {
            media:this.media,
            elementId: this.id,
            options
          })
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
            videoId: this.media.index,
          });
          this.isCollected = true;
        },
        toss() {
          this.$store.dispatch('collection/toss', {
            type: 'youtube',
            mediaId: this.media.id,
          });

          this.isCollected = false;
        },
      },
    };
</script>

<style>

.media-glow {
  -webkit-filter: drop-shadow(12px 12px 7px rgba(0, 0, 0, 0.5));
     filter: drop-shadow(12px 12px 7px rgba(0, 0, 0, 0.5));
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

.media-container {position: relative;} 
.media-container .col {position: absolute; z-index: 1; top: 0; left: 0; color: white; margin-top: 10px;}
</style>
