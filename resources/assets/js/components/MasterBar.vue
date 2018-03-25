<template>
  <div class="container-fluid fixed-bottom">
    <div class="row" v-if="popOutPlayer">
      <div class="col-lg-2">
        <div class="card">
          <div class="card-block">
            <h1>Player</h1>
          </div>
        </div>
      </div>
    </div>

    <nav class="navbar fixed-bottom bg-faded navbar-light rounded" style="background-color:#117F90;">
      <div class="container">
          <div class="row">
            <div class="col-lg-6">
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
      popOutPlayer:false
    }),
    computed: {
      isPlaying() {
        return this.$store.getters['video/isPlaying'];
      },
      volume() {
        return this.$store.getters['video/volume'];
      }
    },
    mounted() {
      console.log("master bar mounted");
    },
    methods: {
      pause() {
        this.playing = false;
        this.$store.dispatch('video/pauseCurrent');
      },
      startQueue() {
        this.playing = true;
        this.$store.dispatch('video/startQueue');
      },
      updateVolume(event) {
        this.$store.dispatch('video/updateVolume', event.target.value);
      },
      playPrevious() {
        this.$store.dispatch('video/playPrevious');
      },
      playNext() {
        this.$store.dispatch('video/playNext');
      }
    },
  };
</script>

<style scoped>
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
