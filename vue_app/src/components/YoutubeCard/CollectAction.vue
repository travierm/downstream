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
            this.inCollection = true
        },
        remove() {
            this.inCollection = false
        },
    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
