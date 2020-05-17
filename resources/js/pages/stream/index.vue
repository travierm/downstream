<template>
  <div class="container-fluid pushFromTop">
    <div class="row">
      <div class="col">
        <h3>Room ID: 1213-34534-12231</h3>
      </div>
    </div>

    <!-- Main Row -->
    <div class="row">
        <!-- Video Player -->
        <div class="col-lg-4 mb-3">

        </div>

        <!-- Search & Player -->
        <div class="col-lg-4 mb-3">
            <b-form-input id="exampleInput1"
                v-on:keyup="searchByQuery"
                type="text"
                v-model="searchQuery"
                required
                placeholder="Search for music to Queue for everyone..">
            </b-form-input>
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
              :videoId="video.vid"
              :title="video.title"
              :thumbnail="video.thumbnail"
            />
          </div>
        </div>
    </div>
  </div>
</template>

<script>
    import io from 'socket.io-client';
    import { numberWithCommas } from '../../services/Utils';

    const socketClient = io.connect("http://192.168.1.11:4444");

    export default {
        data() {
          return {
            firstStart: true,
            show: true,
            searchQuery: "",
            searchResults: [],
            video: false
          }
        },
        computed: {
        },
        mounted() {
          socketClient.on('start_video', (video) => {
            console.log("queue wants to start video " + video.vid);
            this.video = video;

            if(!this.firstStart) {
              this.refreshYouTubeCard()  
            }else{
              this.firstStart = false
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
                    console.log('re-render start')
                    this.$nextTick(() => {
                        console.log('re-render end')
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

                axios.post('/api/stream/search', params).then((response) => {
                  if(response.data) {
                    this.searchResults = response.data;
                  }
                });
                
            }
        }
  };
</script>

<style>
</style>
