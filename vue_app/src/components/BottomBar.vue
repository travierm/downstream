<template>
    <v-app-bar app color="grey darken-4" dense dark fixed bottom>
        <v-container>
            <v-row no-gutters class="justify-left">
                <v-col cols="auto">
                    <v-btn
                        @click="focusOnPlayingCard"
                        color="primary"
                        class="focusBtn ml-2"
                        >Focus</v-btn
                    >
                </v-col>

                <v-col lg="2">
                    <VolumeSlider v-on:update="changeVolume" class="ml-4" />
                </v-col>
            </v-row>
        </v-container>
    </v-app-bar>
</template>

<script>
import { setVolume, getPlayingCardId } from '../services/YoutubePlayerManager'
import VolumeSlider from './VolumeSlider'

export default {
    name: "BottomBar",
    props: {},
    components: {
        VolumeSlider
    },
    data: () => {
        return {}
    },
    mounted() {},
    methods: {
        focusOnPlayingCard() {
            const playingCardGuid = getPlayingCardId();

            if(playingCardGuid) {
                this.$vuetify.goTo('#' + playingCardGuid, {
                    offset: 150,
                    duration: 800,
                    easing: 'easeInOutCubic',
                })
            }
        },
        changeVolume(value) {
            _.debounce(() => {
                setVolume(value)
            }, 450)();
        }
    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.bottom-bar {
    position: fixed;
    background-color: #4a52e8;
    /* background-color: white; */
    bottom: 0%;
}
</style>
