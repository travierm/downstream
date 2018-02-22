<template>
  <div class="container-fluid pushFromTop">

    <div class="row" v-if="!isMobile">
      <div class="col-lg-2">
        <master-bar></master-bar>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6">
        <h1>User Profile</h1>
      </div>

      <div class="col-lg-6">
        <h1>Pinned Song</h1>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-12" v-for="video in currentProfile" :key="video.id">
        <video-player-card v-bind:media="video"></video-player-card>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        userHash:this.$route.params.hash,
        profile:false
      };
    },
    mounted() {
      this.$store.dispatch('video/unregisterAll');

      this.fetchUserProfile(this.userHash);
    },
    watch: {
      '$route' (to, from) {
        console.log('route change ' + this.$route.params.hash);
        this.fetchUserProfile(this.$route.params.hash);
        // react to route changes...
      }
    },
    computed: {
      isMobile() {
        return window._isMobile;
      },
      currentProfile() {
        return this.profile;
      },
      profiles() {
        return this.$store.getters['collection/profiles'];
      },
    },
    methods: {
      fetchUserProfile(hash) {
        let self = this;

        axios.get('/api/media/profile/' + hash).then((resp) => {
          if(resp.status === 200) {
            self.profile = resp.data.collection.youtube;
          }
        });
      }
    },
  };
</script>

<style>
</style>
