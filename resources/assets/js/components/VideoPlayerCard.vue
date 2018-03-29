<template>
  <div class="card">

    <div id="cardToolbar" class="card-block">
      <img class="media-icon" v-if="playing == false" @click="play" height="30" width="30" src="/open-iconic-master/svg/media-play.svg" />
      <img class="media-icon" v-if="playing == true" @click="pause" height="30" width="30" src="/open-iconic-master/svg/media-pause.svg" />
      <div v-if="isAdmin">
        <p class="flex">{{ media.id }}</p>
      </div>

      <div class="float-right">
        <!-- <router-link class="d-inline-flex p-2" :to="media.user.profileLink">Discoverer:<span class="text-success">{{media.user.display_name }}</span></router-link> -->
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
    import $ from 'jquery';
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
        tested: {
          required: false,
          default: false
        },
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
        authed() {
          return window._authed;
        },
        isAdmin() {
          return false;
        },
        videoCollected() {
          return this.isCollected;
        },
      },
      mounted() {
        if(!this.tested) {
          this.registerVideo();
        }
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
          // options.height = $(`.img-fluid`).first().height();
          options.width = $(`#${this.id}`).width();

          const vid = this.media.index;

          this.$store.dispatch('media/indexAdd', this.id);
          this.$store.dispatch('media/videoAdd', { sessionId: this.id, videoId:vid, options});

          this.$store.dispatch('media/registerEvent', {
            sessionId: this.id,
            eventType: ['playing'],
            callback:() => {
              $('#' + this.id).show();
              this.playing = true;
              this.showThumbnail = false;
            }
          })

          this.$store.dispatch('media/registerEvent', {
            sessionId: this.id,
            eventType: ['ended'],
            callback:() => {
              //$('#' + this.id).hide();
              this.playing = false;
              this.showThumbnail = true;
            }
          })

          $('#' + this.id).hide();
        },
        play() {
          this.playing = true;
          this.showThumbnail = false;

          //show player element
          $('#' + this.id).show();

          this.$store.dispatch('media/play', this.id);
        },
        pause() {
          this.playing = false;
          this.$store.dispatch('video/pause');
        },
        discover() {
          this.$store.dispatch('collection/discover', {
            type: 'youtube',
            videoId: this.media.index,
          }).then((err, resp) => {
            this.isCollected = true;
            this.$store.dispatch('media/getCollection');
          }, (err) => {
            this.isCollected = false;
            this.$root.$emit('bv::show::modal','registermodal')
          });
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
