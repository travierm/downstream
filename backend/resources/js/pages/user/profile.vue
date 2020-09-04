<template>
  <div class="container-fluid pushFromTop">
    <div class="justify-content-lg-center" v-if="!authed">
      <h3>We only show Profiles to registered Users!</h3>
      <a class="btn btn-outline-info" href="/register">Register</a>
      <a class="btn btn-outline-success" href="/login">Login</a>
    </div>
    <div v-if="authed">
      <div class="row" v-if="!isMobile">
        <div class="col-lg-2">
          <control-bar></control-bar>
        </div>
      </div>

      <div class="row justify-content-lg-center">
        <div class="col-lg-3">
          <div class="card">
            <div class="card-body">
              <h4>
                {{ user.display_name }}
                <small class="text-muted">{{ user.hash }}</small>
              </h4>
              <h5>Total Collection Size: <span class="text-info">{{ user.media_count }}</span> items.</h5>
              <h5>Joined <span class="text-success">{{ user.days_since_joined }}</span> days ago.</h5>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-lg-3 col-md-6 col-sm-12" v-for="item in userCollection" :key="item.id">
           <youtube-card 
		          :spotifyId="item.source_id" 
		          :video-id="item.index" 
		          :title="item.meta.title" 
		          :thumbnail="item.meta.thumbnail"
				      :collected="item.collected">
		        </youtube-card>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        userHash:this.$route.params.hash,
        collection:false,
        user:{},
        authed:this.$store.state.user.type
      };
    },
    mounted() {
      console.log(this.$store.state.user.id);
      //this.$store.dispatch('video/unregisterAll');

      this.fetchUserProfile(this.userHash);
    },
    watch: {
      '$route' (to, from) {
        this.$store.dispatch('video/unregisterAll');
        this.fetchUserProfile(this.$route.params.hash);
        // react to route changes...
      }
    },
    computed: {
      isMobile() {
        return window._isMobile;
      },
      userCollection() {
        return this.collection;
      }
    },
    methods: {
      fetchUserProfile(hash) {
        let self = this;

        axios.get('/api/media/profile/' + hash).then((resp) => {
          if(resp.status === 200) {
            self.collection = resp.data.collection.youtube;
            self.user = resp.data.user;
            self.failed = false;
          }
        });
      }
    },
  };
</script>

<style>
</style>
