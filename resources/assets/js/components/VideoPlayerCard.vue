<template>
  <div :id="cardId" :class="{ card: true , 'border-info': false, 'mx-auto': true }">

    <div id="cardToolbar" class="card-block">
      <div v-if="false">
        <p class="flex">{{ media.id }}</p>
      </div>

      <div class="float-right">
        <!-- <router-link class="d-inline-flex p-2" :to="media.user.profileLink">Discoverer:<span class="text-success">{{media.user.display_name }}</span></router-link> -->
        <a v-if="reference" :href="'/v/' + reference" class="btn btn-outline-primary">Reference</a>
        <a v-if="media.index" :href="'/v/' + getVid" class="btn btn-outline-success">Link</a>
        <button v-if="!videoCollected" @click="discover" class="btn btn-outline-info">Collect</button>
        <button v-if="videoCollected" @click="toss" class="btn btn-info">Collected</button>
      </div>
    </div>

    <div :id="this.id + '_media'" class="media-container">

      <img @click="play" :id="this.id + '_thumbnail'" class="img-fluid" :src="getThumbnail" v-if="showThumbnail"  />
      <div class="col" v-if="showThumbnail">
        <div class="col-sm-12">
          <p style="color:white;">{{ getTitle }}</p>
        </div>
      </div>
    </div>
    <!-- YouTube Player -->
    <div class="border-success" :id="this.id"></div>
  </div>
</template>

<script>
    import $ from 'jquery';
    import SID from 'shortid';
    let Utils = window._utils;

    

    export default {
      data() {
        return {
          id: SID.generate(),
          playing: false,
          isCollected: false,
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
        preload: {
          required: false,
          default: false
        },
        autoplay: {
          required: false,
          default: false,
        },
        vid: {
          required: false,
        },
        title: {
          default: "",
          required: false
        },
        reference: {
          default: false,
          required: false
        },
        spotifyId: {
          default: false,
          required: false
        },
        thumbnail: {
          default: "",
          required: false
        },
        collected: {
          default: false,
          required: false
        },
        media: {
          required: false,
          default: () => {
            return {
              meta: {
                thumbnail: false,
                title: false,
                vid: false
              }
            }
          }
        }
      },
      computed: {
        getVid() {
          if(this.media.index) {
            return this.media.index;
          }

          return this.vid;
        },
        getTitle(state) {
          if(this.media.meta.title) {
            return this.media.meta.title;
          }

          return this.title;
        },
        getThumbnail(state) {
          if(this.media.meta.thumbnail) {
            return this.media.meta.thumbnail;
          }

          return this.thumbnail;
        },
        hasBadThumbnail() {
          const thumbnail = this.getThumbnail;

          return (thumbnail.includes('/default.jpg') || thumbnail.includes('/hqdefault.jpg'));
        },
        isPlaying(state) {
          return state.playing;
        },
        cardId(state) {
          return state.id + "-card";
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
      created() {
        if(this.media.collected || this.collected) {
          this.isCollected = true;
        }
      },
      mounted() {
        if(this.hasBadThumbnail == true) {
          this.media.meta.thumbnail = "https://via.placeholder.com/640x480/000000?text=" + this.getTitle
        }

        let t = setInterval(() => {
          if (document.readyState === 'complete') {
              // run after page has finished loading
              this.registerVideo();
              clearInterval(t)
          }
        }, 200)
      },
      destroyed() {
        this.$store.dispatch('media/indexRemove', this.id);
      },
      methods: {
        shareMediaLink() {
          const link = window.config.APP_LINK_URL + this.media.index;

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

          const vid = this.getVid;
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
            eventType: ['ended', 'unstarted'],
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
          this.$store.dispatch('media/pause', this.id);
        },
        discover() {
          this.$store.dispatch('collection/discover', {
            type: 'youtube',
            videoId: this.getVid,
            spotifyId: this.spotifyId
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
          }).then((resp) => {
            this.$emit('tossed');
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
