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
                <div class="mt-n2">
                  <v-btn
                    v-if="!isViewingSelf && followingUser !== undefined"
                    depressed
                    color="primary"
                    :loading="followLoading"
                    @click="toggleFollow"
                  >
                    {{ followingUser === false ? 'Follow' : 'Unfollow' }}
                  </v-btn>
                </div>
              </v-row>
            </v-sheet>

            <!-- Profile Top 8 Songs -->
            <v-row>
              <!-- TODO - Currently force reloading card when collected status is changed... There is a better way to handle this -->
              <CardCol
                v-for="item in collectionTop20"
                :key="item.guid + item.collected"
              >
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
import { mapGetters } from 'vuex'

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
  data() {
    return {
      collectionTop20: [],
      profileOwner: {},
      profileIcon: undefined,
      followLoading: false,
    }
  },
  mounted() {
    this.getCollectionTop20()
  },
  computed: {
    ...mapGetters({
      isFollowing: 'follower/isFollowing',
    }),
    user() {
      return this.$store.state.auth.user
    },
    userCollection() {
      return this.$store.state.collection.collection
    },
    routerProfileId() {
      return this.$route.params.profileId
    },
    isViewingSelf() {
      return this.$route.params.profileId == this.user.hash
    },
    followingUser() {
      return this.isFollowing(this.routerProfileId)
    },
  },
  methods: {
    async getCollectionTop20() {
      // current user
      if (this.isViewingSelf) {
        const collectionLength =
          this.userCollection.length >= 20 ? 20 : this.userCollection.length
        this.collectionTop20 = this.userCollection.slice(0, collectionLength)
        this.profileOwner = this.user
        this.profileIcon = identicon(this.user.display_name)
        return
      }

      const result = await CollectionService.fetchCollection(
        false,
        this.$route.params.profileId
      )

      const collection = result.data.items.map((item) => {
        const found = this.userCollection.find(
          (c) => c.media_id == item.media_id
        )

        item.collected = !!found
        return item
      })
      this.collectionTop20 = collection
      this.profileOwner = result.data.user
      this.profileIcon = identicon(result.data.user.display_name)

      this.$store.dispatch('player/updateGuidData', {
        guidIndexKey: this.routerProfileId,
        mediaItems: collection,
      })
    },
    async toggleFollow() {
      if (this.followLoading) {
        return
      }

      try {
        this.followLoading = true

        if (this.followingUser) {
          await this.$store.dispatch('follower/unfollow', this.profileOwner.id)
          return
        }

        await this.$store.dispatch('follower/follow', this.profileOwner.id)
      } catch (error) {
        console.error('Error Following User', error)
      } finally {
        this.followLoading = false
      }
    },
  },
  watch: {
    userCollection(newCollection) {
      if (this.isViewingSelf) {
        // if the logged in users collection is changes update the page
        this.getCollectionTop20()
      } else {
        // if the logged in user collects an item or remove a collected item
        this.collectionTop20 = this.collectionTop20.map((item) => {
          const found = newCollection.find((c) => c.media_id == item.media_id)

          item.collected = !!found
          return item
        })
      }
    },
    $route(to, from) {
      // If the user changes the profile update the page
      if (to.params.profileId != from.params.profileId) {
        this.getCollectionTop20()
      }
    },
  },
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
