<template>
  <nav class="navbar sticky-top navbar-light rounded bg-success">
    <div class="container-fluid">
      <div class="row">
        <div class="Col">
          <img v @click="startQueue" height="30" width="30" src="/open-iconic-master/svg/media-play.svg" />
          <img v-if="playerState == 'playing'" @click="startQueue" height="30" width="30" src="/open-iconic-master/svg/media-pause.svg" />
        </div>
      </div>
    </div>

  </nav>
</template>

<script>
  export default {
    data:() => {
      return {}
    },
    computed: {
      playerState() {
        if(this.$store.state.video.currentVideo) {
          return this.$store.state.video.currentVideo.player.getState();
        }
      }
    },
    methods:{
      startQueue() {
        this.$store.dispatch('video/startVideoQueue');
      }
    }
  }
</script>

<style>
</style>
