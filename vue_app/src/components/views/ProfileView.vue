<template>
  <v-container fluid class="profile-view">
    <v-row>
      <!-- Profile Card -->
      <v-col class="col-md-12 col-12 pt-8">
        <v-card elevation="2" outlined class="pa-4">
          <v-flex grow>
            <!-- Profile Card Header -->
            <v-sheet class="profile-header mb-n6 pa-6" elevation="6">
              <v-row class="mb-1">
                <!-- Profile Icon -->
                <v-avatar size="56" class="mr-3">
                  <div class="profile-icon" v-html="profileIcon"></div>
                </v-avatar>

                <!-- Profile Name -->
                <div class="display-2 font-weight-light">
                  {{ profileOwner.display_name }}
                </div>
              </v-row>

              <!-- Profile Subtitle -->
              <v-row justify="space-between">
                <div class="subtitle-1 font-weight-light">
                  {{ profileOwner.display_name }}'s collection
                </div>

                <!-- Profile actions -->
                <div>
                  <v-btn
                    v-if="user.hash != profileOwner.hash"
                    class="mt-2 mb-2"
                    depressed
                    color="primary"
                  >
                    Follow
                  </v-btn>
                </div>
              </v-row>
            </v-sheet>

            <!-- Profile Top 8 Songs -->
            <v-row>
              <CardCol v-for="item in collectionTop20" :key="item.guid">
                <v-lazy
                  :options="{ threshold: 0.5 }"
                  transition="fade-transition"
                >
                  <YoutubeCard
                    :item="item"
                    :guid="item.guid"
                    :title="item.title"
                    :mediaId="item.media_id"
                    :videoId="item.index"
                    :thumbnail="item.thumbnail"
                    :collected="item.collected"
                    :key="item.guid"
                    dense
                    light
                  ></YoutubeCard>
                </v-lazy>
              </CardCol>
            </v-row>
          </v-flex>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { identicon } from 'minidenticons'

import BottomBar from '@/components/BottomBar'
import CardCol from '@/components/CardCol'
import YoutubeCard from '@/components/YoutubeCard/YoutubeCard'

import CollectionService from '@/services/api/CollectionService'

export default {
  name: 'ProfileView',
  components: {
    BottomBar,
    CardCol,
    YoutubeCard,
  },
  data: () => ({
    collectionTop20: [],
  }),
  mounted() {
    this.getCollectionTop20()
  },
  computed: {
    user() {
      return this.$store.state.auth.user
    },
    profileOwner() {
      if (this.$route.params.profileId === this.user.hash) {
        return this.user
      }

      console.log('Going to view another profile')
      // TODO pull user info based off the hash
      return this.user
    },
    profileIcon() {
      return identicon(this.profileOwner.display_name)
    },
  },
  methods: {
    async getCollectionTop20() {
      if (this.$route.params.profileId == this.user.hash) {
        const collection = this.$store.state.collection.collection
        const collectionLength =
          collection.length >= 20 ? 20 : collection.length
        this.collectionTop20 = collection.slice(0, collectionLength)
      }

      const result = await CollectionService.fetchCollection(
        false,
        this.$route.params.profileId
      )

      const collection = result.data.items
      const collectionLength = collection.length >= 20 ? 20 : collection.length
      this.collectionTop20 = collection.slice(0, collectionLength)
    },
  },
  watch: {},
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
.profile-view {
  .profile-header {
    background-color: #424242;
    position: relative;
    z-index: 1;
    top: -30px;
    width: 100%;
    margin-bottom: -24px !important;
    border-radius: 8px;

    .profile-icon {
      width: 100%;
      height: 100%;
      background-color: #272727;
    }
  }
}
</style>
