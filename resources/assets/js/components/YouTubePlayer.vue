<template>
  <div>
    <p>{{vid}}</p>
    <button class="btn btn-danger" @click="remove(id)">Remove</button>
    <div :id="id"></div>
  </div>
</template>

<script>
  import {mapActions} from 'vuex';
  import id from 'shortid';
  import YouTubePlayer from "youtube-player";

  export default {
    data() {
      return {
        id:id.generate(),
        player:false
      }
    },
    props:{
      mediaId:{
        type: Number,
        required: true
      },
      vid: {
        type: String,
        required: true
      }
    },
    mounted() {
      this.player = YouTubePlayer(this.id, {
        videoId: this.vid,
        width:$('#' + this.id).width()
      });
    },
    destroyed() {
      this.player.destroy();
    },
    methods:{
      remove(id) {
        this.$store.dispatch('frontpage/remove', this.mediaId)
      }
    }
  }
</script>

<style>
</style>
