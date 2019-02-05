<template>
  <div>
    <div class="container pushFromTop">
      <div class="row">
        <div class="Col">
          <button class="btn btn-primary btn-lg" v-on:click="startDemo">Start Demo</button>
          <button class="btn btn-default btn-lg" v-on:click="resetDemo">Reset</button>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-12 mt-5">
          <div class="card">
            <div class="card-header">
              <h4><i class="fas fa-robot"></i> DemoBot </h4>
            </div>
            <div class="card-body">
              <h4 id="writer" class="writer-element"></h4>
            </div>
          </div>
        </div>
      </div>

      <div class="row" v-if="showSearch">
        <div class="col-lg-12 mt-5">
          <transition name="fade">
            <div class="input-group w-75">
              <input v-on:keydown.enter="basicSearch" autofocus id="searchInputBar" v-model="query" name="query" type="search" class="form-control form-control-lg" placeholder="Search...">
              <span class="input-group-append float-right">
                  <button v-on:click="basicSearch()" class="btn btn-primary btn-lg border border-left-0" type="button">
                      <i v-on:click="basicSearch()" class="fa fa-search"></i>
                  </button>
              </span>
            </div>
          </transition>
        </div> 
      </div>

      <div class="row" v-if="results.length >= 1">
         <div class="col-lg-4 col-md-12 col-sm-12 pushFromTop" v-for="video in results" :key="video.id">
            <youtube-card 
              :title="video.title"
              :video-id="video.vid"
              :thumbnail="video.thumbnail"
              >
            </youtube-card>
        </div>
      </div>

      <control-bar v-if="results.length >= 1"></control-bar>
    </div>
  </div>
</template>

<script>
  import { delay } from "../../services/Utils";

  export default {
    data() {
      return {
        query: "",
        results: [],
        showSearch: false
      }
    },
    computed: {},
    mounted() {
      $("footer").hide();
    },
    methods: {
      basicSearch() {
        if(!this.query) {
          return;
        }

        axios.get('/api/demo/search', { params: { query: this.query } })
        .then((resp) => {
          if(resp.data) {
            this.results = resp.data;
            this.afterSearch();
          }
        });
      },
      resetDemo() {
        this.showSearch = false;
        this.query = "";
        this.results = [];
        this.$store.dispatch("player/updatePlayingState", false);
        this.startDemo();
      },
      startDemo() {
        delay(1, () => {
          new TypeIt('#writer', {
            afterComplete: (() => {
              this.showSearch = true;
            })
          })
            .options({
              speed:40
            })
            .type("The demo is starting...")
            .pause(1500)
            .delete()
            .type("Downstream is all about finding and playing content!")
            .pause(1000)
            .break()
            .type("Let's start by finding a song you know.")
            .pause(1000)
            .pause(500)
            .break()
            .break()
            .type("Search for a song or artist you like using the SearchBar below")
            .go();
        })
      },
      afterSearch() {
        this.$store.watch(() => this.$store.getters['player/isPlaying'], () => {
          new TypeIt('#writer', {
            afterComplete: (() => {
            })
          })
            .options({
              speed:40
            })
            .delete()
            .type("You did great!")
            .pause(1500)
            .delete()
            .type("Register with Downstream to collect items permanently and use our discovery feature.")
            .break()
            .type("<a href='/register' class='btn btn-primary'>Register</a>")
            .go();
        });


        new TypeIt('#writer', {
            afterComplete: (() => {
            })
          })
            .options({
              speed:40
            })
            .delete()
            .type("Great job!")
            .pause(1500)
            .delete()
            .type("Play content by clicking the cards image.")
            .go();
      }
    }
  };
</script>

<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}

.writer-element {
  font-family: 'Source Sans Pro', sans-serif;
}
</style>
