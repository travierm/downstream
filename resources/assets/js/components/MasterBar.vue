<template>
  <div class="container">
    <nav class="navbar fixed-bottom bg-faded navbar-light rounded bg-success" style="background-color:#28B463;">
      <div style="width:60%;">
        <img class="icon" @click="playPrevious"height="35" width="35" src="/open-iconic-master/svg/media-step-backward.svg" />
        <img class="icon" @click="startQueue" v-if="!isPlaying" height="35" width="35" src="/open-iconic-master/svg/media-play.svg" />
        <img class="icon" @click="pause" v-if="isPlaying" height="35" width="35" src="/open-iconic-master/svg/media-pause.svg" />
        <img class="icon" @click="playNext" height="35" width="35" src="/open-iconic-master/svg/media-step-forward.svg" />
        <input v-on:change="updateVolume" :value="volume" type="range" min="0" max="100" step="1" style="margin-bottom: -10px;" class="align-middle" />
      </div>
    </nav>
  </div>
</template>

<script>
  import { mapActions } from 'vuex';

  export default {
    data: () => ({
      playing:false,
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

<style>
.icon {
   padding-left: 5px;
}
.iconic-property-fill {
  fill: white;
}
</style>
