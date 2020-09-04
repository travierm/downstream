<template>
  <div class="container">
      <nav class="navbar fixed-bottom bg-faded navbar-dark footer">
        <div class="container">
            <div class="row form-inline">
              <div id="control-bar-items" class="col-lg-12">
                <!-- <button @click="shuffleIndex" type="button" class="btn btn-warning p-2 my-sm-0">Shuffle</button> -->


                <button id="miniPlayerBtn" type="button" class="btn btn-dark p-2 my-sm-0" @click="spawnMiniPlayer()"><i class="fas fa-tv"></i> Mini-Player</button>
                <button type="button" class="btn btn-dark p-2 my-sm-0" @click="focusOnElement(currentCardId)">Focus</button>
                <!-- <img class="icon" @click="playPrevious" height="35" width="35" src="/open-iconic-master/svg/media-step-backward.svg" />
                <img class="icon" @click="play" v-if="!isPlaying" height="35" width="35" src="/open-iconic-master/svg/media-play.svg" />
                <img class="icon" @click="pause" v-if="isPlaying" height="35" width="35" src="/open-iconic-master/svg/media-pause.svg" />
                <img class="icon" @click="playNext" height="35" width="35" src="/open-iconic-master/svg/media-step-forward.svg" /> -->
                
                <!-- <img v-show="!isMobile" id="volume-slider" @click="toggleVolumeSlider" class="icon ml-5" height="35" width="35" src="/open-iconic-master/svg/volume-high.svg" />
                <div v-show="showVolumeSlider" id="slider-vertical" style="height:200px;">  
                </div> -->
                
                <span v-if="currentItem && !isMobile" class="control-bar-title mt-2 ml-3 float-right">{{ currentItem.media.title }}</span>
                <!-- Thumbnail -->
                <div class="float-right">
                  <div v-if="currentItem" class="mt-2 ml-5">
                    <div>
                      <img class="control-bar-thumbnail rounded" :src="currentItem.media.thumbnail" >
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
      </nav> 
  </div>
</template>

<script>
  import { mapActions } from 'vuex';
  import { focusOnElement } from '../services/Utils';

  export default {
    name: "control-bar",
    data: () => ({
      playing:false,
      isMobile: window._isMobile,
      startedQueue: false,
      popOutPlayer:false,
      showVolumeSlider: false
    }),
    mounted() {
    },
    computed: {
      currentItem() {
        return this.$store.getters['player/currentItem'];
      },
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
      focusOnElement:focusOnElement,
      toggleVolumeSlider() {
        $( "#slider-vertical" ).slider({
          orientation: "vertical",
          range: "min",
          min: 0,
          max: 100,
          value: this.volume,
          slide: ( event, ui ) => {
            this.updateVolume(ui.value);
          }
        });

        if(this.showVolumeSlider) {
          this.showVolumeSlider = false;
        }else{
          this.showVolumeSlider = true;
        }
      },
      spawnMiniPlayer() {
        window.open("https://downstream.us/collection", "Downstream", "width=500, height=1050");  // Opens a new window
        window.resizeTo(800, 600);
        window.focus();
      },
      pause() {
        this.playing = false;
        this.$store.dispatch('player/pauseCurrent');
      },
      updateVolume(value) {
        this.$store.dispatch('player/updateVolume', value);
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

#slider-vertical {
  position: element(#volume-slider);
  top: -250px;
  left: 368px;
}

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
  height: 75px;
  width: 100%;
}


</style>
