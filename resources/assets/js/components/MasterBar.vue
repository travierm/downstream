<template>
  <div class="container-fluid">
      <nav class="navbar fixed-bottom bg-faded navbar-light" style="background-color:#117F90;">
        <div class="container">
            <div class="row form-inline">
              <div class="col-lg-8">
                <button type="button" class="btn btn-dark p-2 my-sm-0" v-scroll-to="currentCardId">Focus</button>
                <img class="icon" @click="playPrevious" height="35" width="35" src="/open-iconic-master/svg/media-step-backward.svg" />
                <img class="icon" @click="play" v-if="!isPlaying" height="35" width="35" src="/open-iconic-master/svg/media-play.svg" />
                <img class="icon" @click="pause" v-if="isPlaying" height="35" width="35" src="/open-iconic-master/svg/media-pause.svg" />
                <img class="icon" @click="playNext" height="35" width="35" src="/open-iconic-master/svg/media-step-forward.svg" />
              </div>

              <div class="col-lg-4">
                <input v-if="!isMobile" v-on:change="updateVolume" :value="volume" type="range" min="0" max="100" step="1" style="margin-bottom: -10px;" class="align-middle" />
              </div>
            </div>
        </div>
      </nav>
  </div>
</template>

<script>
  import { mapActions } from 'vuex';

  export default {
    data: () => ({
      playing:false,
      isMobile: window._isMobile,
      startedQueue: false,
      popOutPlayer:false
    }),
    computed: {
      currentCardId() {
        return "#" + this.$store.state.media.current + "-card";
      },
      isPlaying() {
        return this.$store.getters['media/isPlaying'];
      },
      volume() {
        return this.$store.getters['media/playerVolumeLevel'];
      }
    },
    mounted() {
      console.log("master bar mounted");
    },
    methods: {
      focusOnCurrent() {
        let sessionId = this.$store.state.media.current;
        console.log("doin focus " + sessionId);

        $('html, body').animate({
        scrollTop: $("#" + sessionId + "-card").offset().top
        }, 1000);
      },
      pause() {
        this.playing = false;
        this.$store.dispatch('media/pause');
      },
      startQueue() {
        this.playing = true;
        this.startedQueue = true;
        this.$store.dispatch('media/startQueue');
      },
      updateVolume(event) {
        this.$store.dispatch('media/updateVolume', event.target.value);
      },
      play() {
        this.playing = true;

        if(!this.$store.state.media.current) {
          return this.startQueue();
        }
        
        this.$store.dispatch('media/play');
      },
      playPrevious() {
        this.$store.dispatch('media/historyBack');
      },
      playNext() {
        this.$store.dispatch('media/indexNext');
      }
    },
  };
</script>

<style scoped>
#focusBtn {
  padding-top: 10px;
}

.navbar {
  height: 5%;
}

.navbar > li {
  position: relative;
  display: block;
}

.icon {
   padding-left: 5px;
}
.iconic-property-fill {
  fill: white;
}
</style>
