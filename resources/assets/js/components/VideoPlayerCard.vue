<template>
  <div :id="cardId" :class="{ card: true , 'border-info': false, 'mx-auto': true }">

    <div id="cardToolbar" class="card-block">
      <img class="media-icon" v-if="playing == false" @click="play" height="30" width="30" src="/open-iconic-master/svg/media-play.svg" />
      <img class="media-icon" v-if="playing == true" @click="pause" height="30" width="30" src="/open-iconic-master/svg/media-pause.svg" />
      <div v-if="isAdmin">
        <p class="flex">{{ media.id }}</p>
      </div>

      <div class="float-right">
        <!-- <router-link class="d-inline-flex p-2" :to="media.user.profileLink">Discoverer:<span class="text-success">{{media.user.display_name }}</span></router-link> -->
        <button @click="shareMediaLink" class="btn btn-outline-success">Link</button>
        <button v-if="!videoCollected" @click="discover" class="btn btn-outline-info">Collect</button>
        <button v-if="videoCollected" @click="toss" class="btn btn-info">Collected</button>
      </div>
    </div>

    <div :id="this.id + '_media'" class="media-container" v-if="media.meta.thumbnail && this.showThumbnail">

      <img @click="play" :id="this.id + '_thumbnail'" class="img-fluid" :src="thumbnail" />
      <div class="col">
        <div class="col-sm-12">
          <p style="color:white;">{{ media.meta.title }} </p>
        </div>
      </div>
    </div>
    <!-- YouTube Player -->
    <div class="border-success" :id="this.id"></div>
  </div>
</template>

<script>
    import SID from 'shortid';
    import $ from 'jquery';
    let Utils = window._utils;

    function copyToClipboard(text) {
      window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
    }

    export default {
      data() {
        return {
          id: SID.generate(),
          playing: false,
          isCollected: (this.media.collected == true),
          lazyLoad: false,
          thumbnail: this.media.meta.thumbnail,
          showThumbnail: true,
          Utils:Utils
        };
      },
      props: {
        tested: {
          required: false,
          default: false
        },
        preload: {
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
        isPlaying(state) {
          return state.playing;
        },
        cardId(state) {
          return state.id + "-card";
        },
        badThumbnail(state) {
          return (this.media.meta.thumbnail.includes('/default.jpg') || this.media.meta.thumbnail.includes('/hqdefault.jpg'));
        },
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
        if(this.badThumbnail == true) {
          this.thumbnail = "https://via.placeholder.com/640x480/000000?text=" + this.media.meta.title;
        }

        let t = setInterval(() => {
          if (document.readyState === 'complete') {
              // run after page has finished loading
              this.registerVideo();
              clearInterval(t)
          }
        }, 200)
      },
      beforeDestroy() {
      },
      methods: {
        shareMediaLink() {
          const link = "https://down-stream.org/media/" + this.media.index;

          copyToClipboard(link);
        },
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
          options.autoplay = this.autoplay;

          if(this.preload) {
            if(window._isMobile) {
              options.height = 220;
            }else{
              options.height = 380;
            }

            $('#' + this.id).show();
          }else{
            $('#' + this.id).hide();
          }

          const vid = this.media.index;

          this.$store.dispatch('media/indexAdd', this.id);
          this.$store.dispatch('media/videoAdd', { sessionId: this.id, videoId:vid, options});

          if(this.preload) {
            this.$store.dispatch('media/preload', this.id);
          }

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
              $('#' + this.id).hide();
              this.playing = false;
              this.showThumbnail = true;
            }
          })

          this.$store.dispatch('media/registerEvent', {
            sessionId: this.id,
            eventType: ['paused'],
            callback:() => {
              //$('#' + this.id).hide();
              this.playing = false;
            }
          })
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
            $('#modals').show();
            this.$root.$emit('bv::show::modal','registerModal')
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
