<template>
    <v-btn v-if="!inCollection" outlined color="success" @click="collect">
        Collect
        <v-icon class="ml-1" small>{{ mdiMusicNotePlus }}</v-icon>
    </v-btn>

    <v-btn v-else text @click="remove">
        Remove
        <v-icon class="ml-1" color="red" small>{{ mdiMinusCircle }}</v-icon>
    </v-btn>
</template>

<script>
import { mdiMusicNotePlus, mdiMinusCircle } from "@mdi/js"

export default {
    name: "CollectAction",
    props: {
        mediaId: {
            type: Number,
            default: 0,
            required: false,
        },
        videoId: {
            type: String,
            required: true,
        },
        collected: {
            type: Boolean,
            default: false,
            required: true,
        },
    },
    data() {
        return {
            mdiMinusCircle,
            mdiMusicNotePlus,
            inCollection: this.collected,
        }
    },
    methods: {
        collect() {
            this.$store
                .dispatch("collection/collectItem", this.videoId)
                .then(() => {
                    this.$store.dispatch('collection/fetchUserCollection')
                    this.inCollection = true
                })
                .catch(() => {
                    this.inCollection = false
                })
        },
        remove() {
            this.$store
                .dispatch("collection/removeItem", this.mediaId)
                .then(() => {
                    this.$store.dispatch('collection/fetchUserCollection')
                    this.inCollection = false
                })
                .catch(() => {
                    this.inCollection = true
                })
        },
    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
