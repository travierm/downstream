<template>
  <div class="container-fluid">

    <div class="row" style="margin-top: 10px;">
      <div class="col-lg-12">
        <img height="125" width="125" class="img-fluid" src="images/yt_logo_rgb_light.png"></img>
      </div>
    </div>

    <div class="row" style="padding-bottom:10px; margin-top: 10px;">
      <div class="col-lg-3">
        <div class="input-group">
          <input autocomplete="true" @keyup.enter="runQuery" v-model="query" type="text" class="form-control" placeholder="Search for...">
          <span class="input-group-btn">
            <button @click="runQuery" class="btn btn-outline-danger" type="button">Search</button>
          </span>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-12" v-for="media in results" :key="media.index">
        <video-player-card :preload=true :media="media"></video-player-card>
      </div>
    </div>

    <div v-if="!isMobile">
      <master-bar></master-bar>
    </div>
  </div>
</template>

<script>
  import SID from 'shortid';
  window.suggestHandler = function(res) {
    //console.log(res);
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
            this.processResults(resp.data);
          });
      },
      processResults(data) {
        if(data) {
          //clear existing results
          consl("here");
          this.results = [];
        }

        for(var i = 0; i <= data.length - 1; i++) {
          let item = data[i];

          let media = {
            index: item.vid,
            meta: {
              thumbnail: ""
            },
            collected: item.collected
          };

          this.results.push(media);
        }

        consl(this.results);
      }
    },
    computed: {
      isMobile() {
        return window._isMobile;
      }
    },
  };
</script>

<style>
</style>
