
<template>
  <div class="container-fluid" style="margin-top: 15px;">
    <div class="row">
      <div class="col-lg-6 mb-2">
        <input class="form-control" v-model="searchQuery" type="search" placeholder="Search..." />
      </div>
    </div>

    <div class="row">
      <div class="col-lg-3 col-md-12 col-sm-12" v-for="video in filteredVideos" :key="video.id">
        <video-player-card v-on:tossed="updateCollection" v-bind:media="video"></video-player-card>
      </div>
    </div>

    <div class="row hide" v-show="emptyCollection">
      <div class="col-lg-6 center" >
        <h3>Nothing in collection..</h3>
        <img src="https://media.giphy.com/media/hEc4k5pN17GZq/giphy.gif" />
      </div>
    </div>

    <div class="row mt-2 hide" v-show="emptyCollection">
      <div class="col-lg-12">
        <h5>Tools to build your collection...</h5>
        <router-link class="btn btn-outline-danger" to="/search">Search for Music</router-link>
        <a href="/all" class="btn btn-outline-info">See what other people are collecting</a>
      </div>
    </div>

    <master-bar></master-bar>
  </div>
</template>

<script>
  import _ from "lodash";

  export default {
    data() {
      return {
        emptyCollection: false,
        searchQuery:""
      };
    },
    created() {
      this.updateCollection();
    },
    methods: {
      updateCollection() {
        let self = this;

        this.$store.dispatch('collection/update').then(() => {
          if(self.videos.length <= 0) {
            self.emptyCollection = true;
            $(".hide").show();
          }
        });
      }
    },
    watch: {
      'searchQuery':function() {
        //this.$store.dispatch('media/indexClear');
       // this.$store.dispatch('media/indexReplace', _.map(this.filteredVideos, 'sessionId'));
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
        return this.$store.state.collection.items
      },
    },
  };
</script>

<style>
[v-cloak] {
    display: none;
}

.hide = {
  display: none;
}
</style>
