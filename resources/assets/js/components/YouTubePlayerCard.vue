<template>
  <div id="card1" class="card cardHeight">

    <!-- Editor -->
    <div v-if="canEdit" class="card-header">
      <button class="float-right btn btn-sm btn-outline-danger">Remove</button>
    </div>

    <!-- YouTube Player -->
    <div :id="this.id"></div>
    <div class="card-body">
      <div v-if="showText">
        <h4 class="card-title text-truncate">{{title}}</h4>
        <p class="card-text">{{description}}</p>
      </div>

      <img v-if="playing == false" @click="play" height="30" width="30" src="/open-iconic-master/svg/media-play.svg" />

      <img v-if="playing == true" @click="pause" height="30" width="30" src="/open-iconic-master/svg/media-pause.svg" />

      <div class="float-right">
        <button v-if="!collected" @click="importAndCollect" class="btn btn-outline-success">Collect</button>
        <button v-if="collected" @click="toss" class="btn btn-success">Collected</button>
      </div>
    </div>
  </div>
</template>

<script>
    import YouTubePlayer from "youtube-player";
    import SID from 'shortid';

    export default {
      data:() => {
        return {
          playing:false,
          player:false
        }
      },
      props:{
        id: {
          type: String,
          required: true
        },
        vid: {
          type: String,
          required: true
        },
        title: {
          type: String,
          required: false
        },
        description: {
          type: String,
          required: false
        },
        showText: {
          type: Boolean,
          required: false,
          default: true
        },
        collected:{
          type: Boolean,
          required: false,
          default: false
        },
        canEdit: {
          type: Boolean,
          required: false,
          default: false
        }
      },
      mounted() {
        this.player = YouTubePlayer(this.id, {
          videoId: this.vid,
          width:$('#' + this.vid).width()
        });
      },
      methods:{
        play:function() {
          this.playing = true;
          this.player.playVideo();
        },
        pause:function() {
          this.playing = false;
          this.player.stopVideo();
        },
        importAndCollect:function() {
          let self = this;
          $ajax.post('/api/youtube/import', {
            vid:this.vid
          }).then((resp) => {
            if(resp.status == 200) {
              self.collected = true;
            }
          });
        },
        toss:function() {

        }
      }
    }
</script>
