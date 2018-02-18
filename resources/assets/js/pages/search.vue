<template>
  <div class="container-fluid pushFromTop">

    <div class="row" v-if="!isMobile">
      <div class="col-lg-2">
        <master-bar></master-bar>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <img height="125" width="125" class="img-fluid" src="images/yt_logo_rgb_light.png"></img>
      </div>
    </div>

    <div class="row pushFromTop" style="padding-bottom:10px;">
      <div class="col-lg-2">
        <div class="input-group">
          <input autocomplete="true" @keyup.enter="runQuery" v-model="query" type="text" class="form-control" placeholder="Search for...">
          <span class="input-group-btn">
            <button @click="runQuery" class="btn btn-outline-danger" type="button">Query</button>
          </span>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-12" v-for="video in results" :key="video.vid">
        <youtube-player-card v-bind:vid="video.vid" v-bind:collected="video.collected"></youtube-player-card>
      </div>
    </div>

  </div>
</template>

<script>
  window.suggestHandler = function(res) {
    console.log(res);
  }
  export default {
    data() {
      return {
        query:"",
        results: []
      }
    },
    mounted() {
    },
    methods: {
      /*autoSuggest() {
        let query = this.query;
        var url = "https://suggestqueries.google.com/complete/search?hl=en&ds=yt&client=youtube&hjson=t&jsonp=window.suggestHandler&q=" + encodeURIComponent(query) + "&cp=1";
        $.ajax({
          type: "GET",
          url: url,
          dataType: "script",
        });
      }*/
      runQuery() {
        let self = this;
        axios.post('/api/media/search', { type: 'youtube', query: this.query })
          .then((resp) => {
            console.log(resp);
            this.results = resp.data;
          });
      }
    },
    computed: {
      isMobile() {
        return window._isMobile;
      },
      videos() {
        return this.$store.getters['collection/videos'];
      },
    },
  };
</script>

<style>
</style>
