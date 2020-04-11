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
        :id="this.cardId + '_media'"
        :src="thumbnailURL"
        height="435px"
        v-show="showThumbnail"
        @click="handleThumbnailClick"
    ></v-img>

    <div class="video-instance embed-responsive" :id="cardId"></div>
  </v-card>
</template>

<script>
    import YouTubeCardPlayer from '../includes/YouTubeCardPlayer';
    import { generateElementId } from '../includes/GlobalFunctions';
    import $ from 'jquery';

    export default {
        name: 'YouTubeCard',
        props: {
            videoId: String,
            thumbnailURL: String
        },
        data() {
            return {
                cardId: generateElementId(),
                showThumbnail: true
            };
        },
        mounted() {
            this.cardPlayer = new YouTubeCardPlayer(this.videoId, this.cardId)

            this.cardPlayer.registerEventCallback('play', () => {
                console.log("playing");
                this.showThumbnail = false;

                $("#" + this.cardId).show();
            });

            // Reset video once it stops
            this.cardPlayer.registerEventCallback('ended', () => {
                console.log("ended");
                this.showThumbnail = true;

                $("#" + this.cardId).hide();
            });

            // When an error happens show the thumbnauk
            this.cardPlayer.registerEventCallback('unplayable', () => {
                console.log("unplayable");
                this.showThumbnail = true;
            });
        },
        methods: {
            handleThumbnailClick() {
                this.cardPlayer.play();

                this.showThumbnail = false;
            }
        }
    }
</script>