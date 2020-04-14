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
    import $ from 'jquery';
    import YouTubeCardPlayer from '../includes/YouTubeCardPlayer';
    import { generateElementId } from '../includes/GlobalFunctions';

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
                this.handleVideoPlay()
            });

            // Reset video once it stops
            this.cardPlayer.registerEventCallback('ended', () => {
                this.handleVideoStop()
            });

            this.cardPlayer.registerEventCallback('paused', () => {
                this.handleVideoStop()
            });

            // When an error happens show the thumbnauk
            this.cardPlayer.registerEventCallback('unplayable', () => {

                this.handleVideoStop()
            });
        },
        methods: {
            handleVideoPlay() {
                this.showThumbnail = false;

                $("#" + this.cardId).show();
            },
            handleVideoStop() {
                this.showThumbnail = true;

                $("#" + this.cardId).hide();
            },
            handleThumbnailClick() {
                this.cardPlayer.play(true);

                this.showThumbnail = false;
            }
        }
    }
</script>