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
    import { generateElementId } from '../services/GlobalFunctions';
    import YouTubeCardPlayer from '../services/youtube/YouTubeCardPlayer';
    import { getPlayingCardId } from '../services/youtube/YouTubePlayerManager';

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

            this.cardPlayer.registerEventCallback('stopped_by_manager', () => {
                this.handleVideoStop();
            });

            this.cardPlayer.registerEventCallback('paused', () => {
                if(getPlayingCardId(this.cardId)) {
                    // Do nothing if this is the current playing card
                    return true;
                }

                // this.handleVideoStop()
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