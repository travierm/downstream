<template>
  <div class="container-fluid" style="margin-top: 50px;">
      <nav class="navbar fixed-bottom bg-faded navbar-dark footer" style="">
        <div class="container">
            <div class="row form-inline">
              <div class="col-lg-8">
                <!-- <button @click="shuffleIndex" type="button" class="btn btn-warning p-2 my-sm-0">Shuffle</button> -->
                <button type="button" class="btn btn-dark p-2 my-sm-0" v-scroll-to="currentCardId">Focus</button>
                <img class="icon" @click="playPrevious" height="35" width="35" src="/open-iconic-master/svg/media-step-backward.svg" />
                <img class="icon" @click="play" v-if="!isPlaying" height="35" width="35" src="/open-iconic-master/svg/media-play.svg" />
                <img class="icon" @click="pause" v-if="isPlaying" height="35" width="35" src="/open-iconic-master/svg/media-pause.svg" />
                <img class="icon" @click="playNext" height="35" width="35" src="/open-iconic-master/svg/media-step-forward.svg" />
              </div>

              <div class="col-lg-4">
                <p style="margin-top:10px;" v-if="!isMobile">
                  <b>Volume {{ volume }}</b> <br /><input v-on:change="updateVolume" :value="volume" type="range" min="0" max="100" step="1" class="align-middle slider" />
                </p>
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
    methods: {
      shuffleIndex() {
        this.$store.dispatch('media/indexShuffle');
      },
      focusOnCurrent() {
        let sessionId = this.$store.state.media.current;

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
        this.$store.dispatch('player/indexStepForward');
      },
      playNext() {
        this.$store.dispatch('player/indexStepForward');
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

.footer {
  background-color: #4a52e8;
  /* background-color: white; */
  padding:20px;
  left: 0;
  bottom: 0;
  height: 60px;
  width: 100%;
}
</style>
