<template>
  <div class="container-fluid pushFromTop">
    <div class="row">
      <div class="col">
        <h3>Room ID: 1213-34534-12231</h3>
      </div>
    </div>

    <!-- Main Row -->
    <div class="row">
        <!-- Video Connections -->
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
            <ul class="list-unstyled">
                <li class="media" v-for="video in searchResults" :key="video.vid">
                    <img height="64" width="64" :src="video.thumbnail" class="mr-3 img-fluid">
                    <div class="media-body">
                        <h5 class="mt-0 mb-1">{{ video.title }}</h5>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Play Queue -->
        <div class="col">

        </div>
    </div>
  </div>
</template>

<script>
  import { numberWithCommas } from '../../services/Utils';

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
    methods: {
        async searchByQuery() {
            this.searchResults = [];
            if(this.searchQuery.length <= 3) {
                
                // Only do searcg query when at least 3 chars have been typed in
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
