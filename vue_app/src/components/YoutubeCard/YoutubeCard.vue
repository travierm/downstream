<template>
    <v-card class="mx-auto" max-width="580">
        <v-card-actions>
            <v-btn text>Share</v-btn>

            <v-spacer></v-spacer>

            <CollectAction
                :videoId="videoId"
                :mediaId="mediaId"
                :collected="collected"
            />
        </v-card-actions>

        <v-img
            :id="this.cardId + '_media'"
            :src="thumbnail"
            :height="dense ? '250px' : '435px'"
            @click="handleThumbnailClick"
            v-show="showThumbnail"
        >
            <div
                style="width: 90%;"
                class="text-subtitle-1 pl-4 pt-3 d-inline-block text-truncate"
            >
                {{ title || item.meta.title }}
            </div>
        </v-img>

        <div class="video-instance embed-responsive" :id="cardId"></div>
    </v-card>
</template>

<script>
import $ from "jquery"

// Children Components
import CollectAction from "./CollectAction"

import YouTubeCardPlayer from "../../services/YouTubeCardPlayer"
import { generateElementId } from "../../services/GlobalFunctions"
import { getPlayingCardId } from "../../services/YouTubePlayerManager"

export default {
    name: "YoutubeCard",
    components: {
        CollectAction,
    },
    props: {
        title: String,
        mediaId: Number,
        videoId: String,
        thumbnail: String,
        item: {
            type: Object,
            default: {
                meta: {},
            },
        },
        collected: {
            type: Boolean,
            default: false,
        },
        dense: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            cardId: generateElementId(),
            showThumbnail: true,
        }
    },
    mounted() {
        this.cardPlayer = new YouTubeCardPlayer(this.videoId, this.cardId)

        this.cardPlayer.registerEventCallback("play", () => {
            this.handleVideoPlay()
        })

        // Reset video once it stops
        this.cardPlayer.registerEventCallback("ended", () => {
            this.handleVideoStop()
        })

        this.cardPlayer.registerEventCallback("stopped_by_manager", () => {
            this.handleVideoStop()
        })

        this.cardPlayer.registerEventCallback("paused", () => {
            if (getPlayingCardId(this.cardId)) {
                // Do nothing if this is the current playing card
                return true
            }

            // this.handleVideoStop()
        })

        // When an error happens show the thumbnauk
        this.cardPlayer.registerEventCallback("unplayable", () => {
            this.handleVideoStop()
        })
    },
    methods: {
        handleVideoPlay() {
            this.showThumbnail = false

            $("#" + this.cardId).show()
        },
        handleVideoStop() {
            this.showThumbnail = true

            $("#" + this.cardId).hide()
        },
        handleThumbnailClick() {
            this.cardPlayer.play(true)

            this.showThumbnail = false
        },
    },
}
</script>
