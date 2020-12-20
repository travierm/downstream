<template>
    <v-container fluid>

        <v-row v-if="userCollection == undefined || userCollection.length <= 0">
          <v-col>
            <v-progress-circular
              indeterminate
              color="primary"
            ></v-progress-circular>
          </v-col>
        </v-row>

        <v-row v-else>
            <v-col cols="12" sm="3" v-for="item in userCollection" :key="item.id">
              <v-lazy
                :options="{
                  // How much of element should be shown before loading it
                  threshold: .25
                }"
                transition="fade-transition"
              >
                <youtube-card :item="item" :videoId="item.index" :thumbnailURL="item.meta.thumbnail"></youtube-card>
              </v-lazy>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { mapState } from 'vuex';
import YouTubeCard from '@/components/YouTubeCard';

export default {
  name: 'CollectionPage',
  components: {
    'youtube-card': YouTubeCard
  },
  computed: {
    ...mapState({
      userCollection: state => state.collection.userCollection
    })
  },
  data: () => {
    return {}
  },
  mounted() {
    // this.$store.dispatch('collection/fetchUserCollection')
  },
  methods:{
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
