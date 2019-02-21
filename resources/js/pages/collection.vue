
<template>
  <div class="container-fluid" style="margin-top: 15px;">
    <div class="row mb-3">
      <div v-if="!isMobile && videos.length >= 1" class="col-lg-6 mb-2">
        <input class="form-control" v-model="searchQuery" type="search" placeholder="Search your collection..." />
      </div>

      <div v-if="videos.length >= 1" class="col-lg-6">
        <div class="float-right">
          <a href="/collection" v-if="randomize" class="btn btn-primary">Randomized</a>
          <a href="/collection/random" v-if="!randomize" class="btn btn-outline-primary">Randomize</a>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-12" v-for="item in filteredVideos" :key="item.id">
        <youtube-card
            :showGlobalQueue="true"
            :globalQueued="item.globalQueued"
            :media-id="item.id"
            :session-id="item.sessionId"
            :videoId="item.index"
            :title="item.meta.title"
            :thumbnail="item.meta.thumbnail"
            :owner-id="item.user_id"
            :collected="item.collected"
        />
      </div>
    </div>

    <div class="row" v-if="videos.length <= 0 && !emptyCollection">
       <div class="col-lg-12">
        <h1 class="text-center">Loading Collection...</h1>
      </div>
    </div>

    <div class="row mt-2 d-flex justify-content-center"  v-if="emptyCollection">
       <div class="col-lg-3" >
        <div class="alert alert-warning" role="alert">
          <strong>Your collection is empty!</strong> Use the search bar to find music to collect.
        </div>
        <img style="width:100%" src="https://media.giphy.com/media/hEc4k5pN17GZq/giphy.gif" />
      </div>
    </div>

    <control-bar></control-bar>
  </div>
</template>

<script>
  import _ from "lodash";

  let firstHit = true;

  export default {
    data() {
      return {
        randomize: false,
        searchQuery:"",
        emptyCollection: false
      };
    },
    created() {
      //this.updateCollection();
       if(this.$store.state.route.params.filter === "random") {
          this.randomize = true;
        }else{
          this.randomize = false;
        }
    },
    methods: {
      updateCollection() {
        //BIG
        return;
        
        let self = this;
        this.$store.dispatch('collection/update', this.randomize).then(() => {
          if(self.videos.length <= 0) {
            self.emptyCollection = true;
          }
        });
      }
    },
    watch: {
      'searchQuery':function() {
        let indexes =  _.map(this.filteredVideos, 'sessionId');
        this.$store.dispatch('player/indexReplace', indexes);
      }
    },
    computed: {
      filteredVideos() {
        return this.videos.filter(video => {
          return video.meta.title.toLowerCase().includes(this.searchQuery.toLowerCase())
        })
      },
      isMobile() {
        return window._isMobile;
      },
      videos() {
        let videos = this.$store.getters['collection/getItems'];

        if(videos.length <= 0 && firstHit == false) {
          this.emptyCollection = true;
        }else{
          firstHit = false;
        }

        return videos;
      },
    },
  };
</script>

<style>
[v-cloak] {
    display: none;
}

.hide {
  display: none;
}
</style>
