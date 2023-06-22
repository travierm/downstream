<template>
    <v-container fluid class="pt-0">
      <h1 class="text-center my-2 ">Follower Feed</h1>
      <v-row>
        <CardCol v-for="video in items" :key="video.guid">
          <YoutubeCard
            :item="video"
            :guid="video.guid"
            :title="video.title"
            :mediaId="video.mediaId"
            :videoId="video.index"
            :collected="video.collected"
            :thumbnail="video.thumbnail"
            :collected-by="video.object_meta.collected_by"
            :collected-at="video.object_meta.collected_at"
          ></YoutubeCard>
        </CardCol>
      </v-row>
    </v-container>
</template>

<script>
import YoutubeCard from '@/components/YoutubeCard/YoutubeCard';
import { getFollowingActivites } from '@/services/api/FollowerService';
import { getCurrentPathFromURL } from '@/services/GlobalFunctions'

export default {
  name: 'FollowerActivityView',
  components: {
    'youtube-card': YoutubeCard
  },
  data: () => {
    return {
      items: null
    }
  },
  mounted() {
    getFollowingActivites().then((data) => {
      this.items = data.items;

      this.$store.dispatch('player/updateGuidData', {
        guidIndexKey: getCurrentPathFromURL(),
        mediaItems: this.items
      })
    })
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
