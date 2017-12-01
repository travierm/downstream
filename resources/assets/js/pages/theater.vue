<!--
Start with initial mediaId
Goto /api/theater to get a queue built
Play current media
Move to next media
-->
<template>
  <div class="container-fluid pushFromTop">
    <div class="row">
    </div>
  </div>
</template>

<script>
  import { mapGetters, mapActions } from 'vuex'

  export default {
    data() {
      return {
        currentMediaId: this.$route.params.mediaId,
        player: false,
        playing: false
      };
    },
    mounted() {
      this.$store.dispatch('media/getTheaterQueue', this.currentMediaId);
    },
    computed: {
      videos() {
        return this.$store.state.frontpage.videos;
      }
    }
  }
</script>

<style>
</style>
