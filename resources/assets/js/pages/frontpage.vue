<template>
  <div class="container-fluid pushFromTop">
    <div class="row">
      <div class="col-lg-3"  v-for="video in videos" :key="video.id">
        <youtube-player  :media-id="video.id" :vid="video.vid"></youtube-player>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapGetters, mapActions } from 'vuex'

  export default {
    mounted() {
      this.$store.dispatch('frontpage/update', [
        {
          id:1,
          vid:"BPj7RptdwxE"
        },
        {
          id:2,
          vid:"SDHual6GEWM"
        },
        {
          id:3,
          vid:"GlvBwpRLjz4"
        }
      ]);
    },
    methods:{
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
