
<template>
  <div class="container-fluid" style="margin-top: 15px;">
    <div class="row mb-3">
      <div v-if="!isMobile" class="col-lg-6 mb-2">
        <input class="form-control" v-model="searchQuery" type="search" placeholder="Search your collection..." />
      </div>

      <div class="col-lg-6">
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

    <div class="row" v-show="emptyCollection">
      <div class="col-lg-6 center" >
        <h3>Nothing in collection..</h3>
        <img src="https://media.giphy.com/media/hEc4k5pN17GZq/giphy.gif" />
      </div>
    </div>

    <div class="row mt-2"  v-show="emptyCollection">
      <div class="col-lg-12">
        <h5>Some tools to build your collection:</h5>
        <a class="btn btn-outline-danger" href="/search">Search for Music</a>
        <a href="/all" class="btn btn-outline-info">See what other people are collecting</a>
      </div>
    </div>

    <control-bar></control-bar>
  </div>
</template>

<script>
  import _ from "lodash";

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
        return this.$store.getters['collection/getItems'];
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
