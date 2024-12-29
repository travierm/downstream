<template>
  <v-container fluid class="landingViewContent">
    <v-row justify="center">
      <v-col cols="12">
        <!-- <h1 class="ds-gradient">downstream</h1> -->
        <h2>A private music collection service</h2>

        <v-btn color="primary" to="/login" class="mt-4 mb-4" medium>
          Login
        </v-btn>

        <v-btn color="primary" to="/waitlist" class="mt-4 mb-4 ml-2 text-dark" medium>Join our waiting list</v-btn>
        <v-btn color="primary" to="/waitlist" class="mt-4 mb-4 ml-2" medium>Signup with Invite Code</v-btn>
      </v-col>

      <CardCol class="text-xs-center">
        <PlayableYoutubeCard :guid="'m_' + selectedMedia.videoId" :videoId="selectedMedia.videoId"
          :title="selectedMedia.title" :thumbnail="selectedMedia.thumbnail" :hide-actions="true"></PlayableYoutubeCard>
      </CardCol>
    </v-row>

    <v-row>
      <div class="d-flex" style="gap: 8px; justify-content: center; width: 100%;">
        <router-link to="/privacy" class="unstyled-link">
          Privacy
        </router-link>

        <router-link to="/tos" class="unstyled-link">
          ToS
        </router-link>

        <a class="unstyled-link" href="https://www.youtube.com/t/terms">YouTube ToS</a>
      </div>
    </v-row>
  </v-container>
</template>

<script>
import CardCol from '../CardCol'
import { getAppVersion } from '@/services/GlobalFunctions'
import PlayableYoutubeCard from '@/components/YoutubeCard/PlayableYoutubeCard'
import { mdiMusicNotePlus } from '@mdi/js';

const demoMediaItems = [
  {
    videoId: 'OcnuAHbI2WI',
    title: 'Kuzu Mellow sunflower feelings (prod. by koruo)',
    thumbnail: 'https://i.ytimg.com/vi/OcnuAHbI2WI/sddefault.jpg',
  },
  // {
  //   videoId: 'm_qlgFQs7E4',
  //   title: 'Yas - Empty Crown',
  //   thumbnail: 'https://i.ytimg.com/vi/m_qlgFQs7E4/sddefault.jpg',
  // },
  {
    videoId: '6wi1nJWk-QE',
    title: 'lofi.samurai - water',
    thumbnail: 'https://i.ytimg.com/vi/6wi1nJWk-QE/hqdefault.jpg',
  },
  {
    videoId: 'a0muICdfyPc',
    title: 'KOVEN - Gold',
    thumbnail: 'https://i.ytimg.com/vi/a0muICdfyPc/hqdefault.jpg',
  },
  {
    title: 'Dreweybear - The Rise (Ft. Ashley Apollodor)',
    videoId: '1mEQcaURtws',
    thumbnail: 'https://i.ytimg.com/vi/1mEQcaURtws/hqdefault.jpg',
  },
]

//

export default {
  name: 'LandingView',
  components: {
    CardCol,
    PlayableYoutubeCard,
  },
  computed: {
    selectedMedia() {
      if (this.demoMediaItems.length == 1) {
        return this.demoMediaItems[0]
      }

      const rng = this.getRandomInt(0, this.demoMediaItems.length - 1)

      return this.demoMediaItems[rng]
    },
  },
  data: () => {
    return {
      demoMediaItems: demoMediaItems,
    }
  },
  methods: {
    getAppVersion,
    getRandomInt(min, max) {
      min = Math.ceil(min)
      max = Math.floor(max)
      return Math.floor(Math.random() * (max - min + 1)) + min
    },
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.landingViewContent {
  text-align: center;
}
</style>
