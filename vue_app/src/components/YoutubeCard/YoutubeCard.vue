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
            :id="this.guid + '_media'"
            :src="thumbnail"
            :height="dense ? '250px' : '435px'"
            @click="handleThumbnailClick"
            v-if="showThumbnail"
        >
            <div
                style="width: 90%;"
                class="text-subtitle-1 pl-4 pt-3 d-inline-block text-truncate youtubeCardTitle"
            >
                {{ title }}
            </div>
        </v-img>

        <div class="video-instance embed-responsive" :id="guid"></div>
    </v-card>
</template>

<script>
import $ from "jquery"

// Children Components
import CollectAction from "./CollectAction"

import YouTubeCardPlayer from "../../services/YouTubeCardPlayer"
import { getPlayingCardId } from "../../services/YoutubePlayerManager"

export default {
    name: "YoutubeCard",
    components: {
        CollectAction,
    },
    props: {
        guid: String,
        title: String,
        mediaId: Number,
        videoId: String,
        thumbnail: String,
        guid: {
            type: String,
            required: true,
        },
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
            showThumbnail: true,
        }
    },
    mounted() {
        this.cardPlayer = new YouTubeCardPlayer(this.guid, this.videoId)

        // Register events so we can update our view on player state changes
        this.cardPlayer.on("play", () => {
            this.handleVideoPlay()
        })

        // Reset video once it stops
        this.cardPlayer.on("ended", () => {
            this.handleVideoStop()
        })

        this.cardPlayer.on("stopped_by_manager", () => {
            this.handleVideoStop()
        })

        this.cardPlayer.on("paused", () => {
            if (getPlayingCardId(this.guid)) {
                // Do nothing if this is the current playing card
                return true
            }

            // this.handleVideoStop()
        })

        // When an error happens show the thumbnauk
        this.cardPlayer.on("unplayable", () => {
            this.handleVideoStop()
        })
    },
    methods: {
        handleVideoPlay() {
            this.showThumbnail = false

            $("#" + this.guid).show()

            console.log("DISPLAY play event triggered " + this.guid)
            this.$set(this, 'showThumbnail', false)
        },
        handleVideoStop() {
            console.log("DISPLAY stop event triggered " + this.guid)

            this.showThumbnail = true

            $("#" + this.guid).hide()
        },
        handleThumbnailClick() {
            this.cardPlayer.play()
        },
    },
}
</script>
