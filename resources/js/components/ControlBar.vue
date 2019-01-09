<template>
  <div class="container" style="margin-top: 50px;">
      <nav class="navbar fixed-bottom bg-faded navbar-dark footer" style="">
        <div class="container">
            <div class="row form-inline">
              <div class="col-lg-9">
                <!-- <button @click="shuffleIndex" type="button" class="btn btn-warning p-2 my-sm-0">Shuffle</button> -->
                <button id="miniPlayerBtn" type="button" class="btn btn-dark p-2 my-sm-0" @click="spawnMiniPlayer()"><i class="fas fa-tv"></i> Mini-Player</button>
                <button type="button" class="btn btn-dark p-2 my-sm-0" @click="focusOnCard(currentCardId)">Focus</button>
                <img class="icon" @click="playPrevious" height="35" width="35" src="/open-iconic-master/svg/media-step-backward.svg" />
                <img class="icon" @click="play" v-if="!isPlaying" height="35" width="35" src="/open-iconic-master/svg/media-play.svg" />
                <img class="icon" @click="pause" v-if="isPlaying" height="35" width="35" src="/open-iconic-master/svg/media-pause.svg" />
                <img class="icon" @click="playNext" height="35" width="35" src="/open-iconic-master/svg/media-step-forward.svg" />
              </div>

              <div id="control-bar-volume-container" class="col-lg-3">
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
  import VueScrollTo from 'vue-scrollto';

  export default {
    data: () => ({
      playing:false,
      isMobile: window._isMobile,
      startedQueue: false,
      popOutPlayer:false
    }),
    computed: {
      currentCardId() {
        return this.$store.state.player.currentId + "_card";
      },
      isPlaying() {
        return this.$store.getters['player/isPlaying'];
      },
      volume() {
        return this.$store.getters['player/getVolume'];
      }
    },
    methods: {
      spawnMiniPlayer() {
        window.open("https://downstream.us/collection", "Downstream", "width=500, height=1050");  // Opens a new window
        window.resizeTo(800, 600);
        window.focus();
      },
      focusOnCard(element) {
        const query = "[id='" + element + "']";

        VueScrollTo.scrollTo(query);
      },
      pause() {
        this.playing = false;
        this.$store.dispatch('player/pauseCurrent');
      },
      updateVolume(event) {
        this.$store.dispatch('player/updateVolume', event.target.value);
      },
      play() {
        this.playing = true;

        if(!this.$store.state.player.currentId) {
          this.$store.dispatch('player/indexStepForward');
        }
        
        this.$store.dispatch('player/playCurrent');
      },
      playPrevious() {
        this.$store.dispatch('player/indexStepBackward');
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
