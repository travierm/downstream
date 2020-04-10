<template>
    <v-container fluid>
      <v-row>
        <v-col cols="12" sm="3" v-for="video in videos" :key="video.id">
          <youtube-card :videoID="video.videoID" :thumbnailURL="video.thumbnailURL"></youtube-card>
        </v-col>
      </v-row>
    </v-container>
</template>

<script>
import YouTubeCard from '../components/YouTubeCard';

export default {
  name: 'Collection',
  components: {
    'youtube-card': YouTubeCard
  },
  data: () => {
    return {
      videos:[
        {
          id:'test',
          videoID: '6m1YYAMA1kM',
          thumbnailURL: "https://i.ytimg.com/vi/6m1YYAMA1kM/sddefault.jpg"
        },
        {
          id:'test2',
          videoID: 'n7Uc5IscsC8',
          thumbnailURL: "https://i.ytimg.com/vi/iqurYVWg7-g/hqdefault.jpg"
        },
        {
          id:'test3',
          videoID: 'Jm5DjptGtJo',
          thumbnailURL: "https://i.ytimg.com/vi/Jm5DjptGtJo/sddefault.jpg"
        },
        {
          id: 'test4',
          videoID: 'ygTZZpVkmKg',
          thumbnailURL: "https://i.ytimg.com/vi/ygTZZpVkmKg/sddefault.jpg"
        },
      ]
    }
  },
  methods:{
    fetchCollection() {
      
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
