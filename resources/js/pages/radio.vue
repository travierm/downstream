<template>
  <div class="container-fluid pushFromTop">
    <div class="row">
        <div class="col-lg-6" @keyup.enter="seedRadio">
            <h1><i class="fas fa-broadcast-tower"></i> Radio</h1>
            <b-form-group id="exampleInputGroup1"
                    label="Seed Query"
                    label-for="query"
                    description="Use a search query to act as a seed for starting your radio">
                <b-form-input id="exampleInput1"
                            
                            type="text"
                            v-model="query"
                            required
                            placeholder="Artist, Track Name or Music Genre">
                </b-form-input>
            </b-form-group>
            <b-button @click="seedRadio" type="submit" variant="primary">Start Radio</b-button>
        </div>
    </div>
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-12" v-for="item in radioItems" :key="item.id">
        <youtube-card
            :media-id="item.id"
            :videoId="item.vid"
            :title="item.title"
            :thumbnail="item.thumbnail"
            :collected="item.collected"
        />
      </div>
    </div>

    <control-bar></control-bar>
  </div>
</template>

<script>
  import { numberWithCommas } from '../services/Utils';

  export default {
    data() {
      return {
        query: "",
        radioItems: [],
        itemCount: 0,
        userCount: 0,
        queueCount: 0,
      }
    },
    computed: {
      itemCountWithCommas() {
        return numberWithCommas(this.itemCount);
      }
    },
    mounted() {
      let self = this;
      axios.get('/api/stat/library/size').then((resp) => {
        self.itemCount = resp.data.count;
      });
    },
    methods: {
        seedRadio() {
            const query = this.query;

            axios.get('/api/radio/seed?query=' + query).then((resp) => {
                this.radioItems = resp.data;
            });
        }
    }
  };
</script>

<style>
</style>
