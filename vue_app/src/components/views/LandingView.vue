<template>
  <v-container fluid class="landingViewContent">
    <v-row justify="center">
      <v-col cols="12">
        <h2>Welcome to downstream</h2>

        <h3 class="mt-2">This site is currently under development.</h3>

        <v-btn to="/login" class="mt-4 mb-4" large outlined>
          Login
        </v-btn>
      </v-col>

      <CardCol class="text-xs-center">
        <YoutubeCard
          :guid="'m_' + selectedMedia.videoId"
          :videoId="selectedMedia.videoId"
          :title="selectedMedia.title"
          :thumbnail="selectedMedia.thumbnail"
          :hide-actions="true"
        ></YoutubeCard>
      </CardCol>
    </v-row>
  </v-container>
</template>

<script>
import CardCol from '../CardCol'
import YoutubeCard from '@/components/YoutubeCard/YoutubeCard'

const demoMediaItems = [
  {
    videoId: 'OcnuAHbI2WI',
    title: 'Kuzu Mellow sunflower feelings (prod. by koruo)',
    thumbnail: 'https://i.ytimg.com/vi/OcnuAHbI2WI/sddefault.jpg',
  },
  {
    videoId: 'm_qlgFQs7E4',
    title: 'Yas - Empty Crown',
    thumbnail: 'https://i.ytimg.com/vi/m_qlgFQs7E4/sddefault.jpg',
  },
  {
    videoId: '6CJ6kKEXHyA',
    title: 'Hectorino Martinez - You Say',
    thumbnail: 'https://i.ytimg.com/vi/6CJ6kKEXHyA/hqdefault.jpg',
  },
  {
    videoId: '6wi1nJWk-QE',
    title: 'lofi.samurai - water',
    thumbnail: 'https://i.ytimg.com/vi/6wi1nJWk-QE/hqdefault.jpg',
  },
]

//

export default {
  name: 'LandingView',
  components: {
    CardCol,
    YoutubeCard,
  },
  computed: {
    selectedMedia() {
      if (this.demoMediaItems.length == 1) {
        return this.demoMediaItems[0]
      }

      const rng = this.getRandomInt(0, this.demoMediaItems.length - 1)
      console.log(rng)

      return this.demoMediaItems[rng]
    },
  },
  data: () => {
    return {
      demoMediaItems: demoMediaItems,
    }
  },
  methods: {
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
