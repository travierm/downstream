<template>
  <v-card
    class="mx-auto"
    max-width="580"
  >
    <v-card-actions v-if="share || collect">
      <v-btn v-if="share" text>Share</v-btn>

      <v-spacer></v-spacer>

      <v-btn v-if="collect" icon>
        <v-icon>mdi-heart</v-icon>
      </v-btn>
    </v-card-actions>

    <v-img
        :id="this.cardId + '_media'"
        :src="thumbnailURL"
        :height="dense ? '250px' : '435px'"
        v-show="showThumbnail"
        @click="handleThumbnailClick"
    >
        <div style="width: 90%;" class="text-subtitle-1 pl-4 pt-3 d-inline-block text-truncate">{{ item.meta.title }}</div>
    </v-img>

    <div class="video-instance embed-responsive" :id="cardId"></div>
  </v-card>
</template>

<script>
    import $ from 'jquery';
    import YouTubeCardPlayer from '../includes/YouTubeCardPlayer';
    import { generateElementId } from '../includes/GlobalFunctions';
    import { getPlayingCardId } from '../includes/YouTubePlayerManager';

    export default {
        name: 'YouTubeCard',
        props: {
            videoId: String,
            thumbnailURL: String,
            item: {
                type: Object,
                default: {
                    meta: {}
                }
            },
            share: {
                type: Boolean,
                default: true
            },
            collect: {
                type: Boolean,
                default: true
            },
            dense: {
                type: Boolean,
                default: false
            }
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
                console.log('pause!');

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