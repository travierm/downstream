<template>
  <v-card
    class="mx-auto"
    max-width="580"
  >
    <v-card-actions>
      <v-btn text>Share</v-btn>

      <v-spacer></v-spacer>

      <v-btn icon>
        <v-icon class="mr-1">mdi-heart</v-icon>
      </v-btn>
    </v-card-actions>

    <v-img
        :id="this.playerElementID + '_media'"
        :src="thumbnailURL"
        height="435px"
        v-show="showThumbnail"
        @click="handleThumbnailClick"
    ></v-img>

    <div class="video-instance embed-responsive" :id="playerElementID"></div>
  </v-card>
</template>

<script>
    import YouTubeCardPlayer from '../includes/YouTubeCardPlayer';

    function generateElementId() {
        return Math.random().toString(36).substr(2, 9);
    }

    export default {
        name: 'YouTubeCard',
        props: {
            videoID: String,
            thumbnailURL: String
        },
        data() {
            return {
                playerElementID: generateElementId(),
                showThumbnail: true
            };
        },
        mounted() {
            this.youtubePlayer = new YouTubeCardPlayer(this.videoID, this.playerElementID)
        },
        methods: {
            handleThumbnailClick() {
                this.youtubePlayer.play();

                this.showThumbnail = false;
            }
        }
    }
</script>