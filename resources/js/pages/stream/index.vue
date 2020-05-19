<template>
  <div class="container-fluid pushFromTop">
    <div class="row">
      <div class="col">
        <h3>Music Streaming!</h3>
      </div>
    </div>

    <!-- Main Row -->
    <div class="row">
        <!-- Search & Player -->
        <div class="col-lg-4 mb-3">
            <div class="form-group">
              <label>Search for music to Queue for everyone...</label>
              <input placeholder="Search for music on YouTube!" type="text" class="form-control" v-on:keyup="searchByQuery" v-model="searchQuery" />
            </div>
            <!-- Search Results -->

            <table class="table" v-if="searchResults.length >= 1">
              <tbody>
                <tr v-for="video in searchResults" :key="video.vid">
                  <td>
                    <img height="64" width="64" :src="video.thumbnail" class="mr-3 img-fluid">
                  </td>
                  <td>{{ video.title.substring(0, 60) }}</td>
                  <td>
                    <button class="btn btn-primary" @click="queueVideoId(video)">Queue</button>
                  </td>
                </tr>
              </tbody>
            </table>
        </div>

        <!-- Video Connections -->
        <div class="col-lg-4 mb-3">
          <div v-if="show && video.vid">
            <youtube-card
              :shouldPlay="true"
              :shouldPlayNext="false"
              :startAtTimestamp="video.currentTimestamp"
              :videoId="video.vid"
              :title="video.title"
              :thumbnail="video.thumbnail"
            />
          </div>
        </div>

        <div class="col-lg-3 mb-3">
          <h4>Play Queue</h4>
            <table class="queueTable" v-if="videoQueue.length >= 1">
              <tr v-for="video in videoQueue" :key="video.vid">
                <td>
                  <img height="64" width="64" :src="video.thumbnail" class="mr-3 img-fluid">
                </td>
                <td>{{ video.title.substring(0, 60) }}</td>
              </tr>
            </table>
            <p v-else>No video in the queue...</p>
        </div>
    </div>
  </div>
</template>

<script>
    import io from 'socket.io-client';
    import { numberWithCommas } from '../../services/Utils';

    var globalKeyPressTimeout = null;  
    const socketClient = io.connect("http://downstream.us:4444");
    var fireOnce = false;

    export default {
        data() {
          return {
            firstStart: true,
            show: true,
            searchQuery: "",
            searchResults: [],
            videoQueue: [],
            video: false
          }
        },
        computed: {
        },
        mounted() {

          socketClient.on('receive_video_queue', (videoQueue) => {
            this.videoQueue = videoQueue
          });

          socketClient.on('start_video_at_secs', (video) => {
            this.video = video;

            this.refreshYouTubeCard()  
          })

          socketClient.on('start_video', (video) => {
            console.log("queue wants to start video " + video.vid);
            this.video = video;

            if(!fireOnce) {
              console.log('card refresh');
              this.refreshYouTubeCard()  
            }else{
              fireOnce = false
            }
          });
        },
        watch: {
            searchQuery: (val) => {
                if(val == "") {
                    this.searchResults = []
                }
            }
        },
        methods: {
            refreshYouTubeCard(){
                this.show = false
                this.$nextTick(() => {
                    this.show = true
                    this.$nextTick(() => {
                    })
                })
            },
            queueVideoId(videoId) {
                socketClient.emit('queue_video', videoId);
            },
            async searchByQuery() {
                if(this.searchQuery == "") {
                    this.searchResults = [];
                }

                if(this.searchQuery.length <= 3) {
                    // Only do search query when at least 3 chars have been typed in
                    return;
                }

                const params = {
                    query: this.searchQuery
                };

                if(globalKeyPressTimeout != null) clearTimeout(globalKeyPressTimeout);

                // Dont allow a ton of request per type
                globalKeyPressTimeout = setTimeout(() => {
                  axios.post('/api/stream/search', params).then((response) => {
                    if(response.data) {
                      this.searchResults = response.data;
                    }
                  });
                }, 500);
            }
        }
  };
</script>

<style>
.queueTable {
    height: 100px !important;
    overflow: scroll;
}
</style>
