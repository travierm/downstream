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
        <div class="col">

        </div>

        <!-- Search & Player -->
        <div class="col">
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
        <div class="col">

        </div>
    </div>
  </div>
</template>

<script>
    import io from 'socket.io-client';
    import { numberWithCommas } from '../../services/Utils';

    const socketClient = io.connect("http://localhost:4444");

    export default {
        data() {
            return {
            searchQuery: "",
            searchResults: []
            }
        },
        computed: {
        },
        mounted() {
        },
        watch: {
            searchQuery: (val) => {
                if(val == "") {
                    this.searchResults = []
                }
            }
        },
        methods: {
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

                const response  = await axios.post('/api/stream/search', params);
                if(response.data) {
                    this.searchResults = response.data;
                }
            }
        }
  };
</script>

<style>
</style>
