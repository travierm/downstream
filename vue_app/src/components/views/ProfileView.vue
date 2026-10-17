<template>
  <v-container fluid class="profile-view">
    <header class="profile-header">
      <div class="profile-identity">
        <v-avatar size="64" class="profile-avatar">
          <div class="profile-icon" v-html="profileIcon" aria-hidden="true"></div>
        </v-avatar>
        <div class="profile-details">
          <h1 class="profile-name">{{ profileOwner.display_name }}</h1>
          <p class="profile-subtitle">Music collection</p>
        </div>
      </div>

      <v-btn
        v-if="!isViewingSelf && followingUser !== undefined"
        class="profile-follow"
        :variant="followingUser ? 'tonal' : 'flat'"
        color="primary"
        :loading="followLoading"
        @click="toggleFollow"
      >
        {{ followingUser === false ? 'Follow' : 'Unfollow' }}
      </v-btn>
    </header>

    <v-row>
      <!-- Reload the card when its collected status changes. -->
      <CardCol v-for="item in collectionTop20" :key="item.guid + item.collected">
        <v-lazy :options="{ threshold: 0.5 }" transition="fade-transition">
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
  </v-container>
</template>

<script>
import { identicon } from 'minidenticons'
import { mapGetters } from 'vuex'

import CardCol from '@/components/CardCol'
import YoutubeCard from '@/components/YoutubeCard/YoutubeCard'

import CollectionService from '@/services/api/CollectionService'
import { getCurrentPathFromURL } from '@/services/GlobalFunctions'

export default {
  name: 'ProfileView',
  components: {
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
        guidIndexKey: getCurrentPathFromURL(),
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

<style scoped lang="scss">
.profile-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 20px;
  padding: 24px 0 32px;
}

.profile-identity {
  display: flex;
  align-items: center;
  gap: 16px;
  min-width: 0;
  flex: 1 1 240px;
}

.profile-avatar {
  flex-shrink: 0;
  background: rgb(var(--v-theme-surface));
}

.profile-icon {
  width: 100%;
  height: 100%;
}

.profile-details {
  min-width: 0;
}

.profile-name {
  margin: 0;
  font-size: clamp(1.5rem, 3vw, 2rem);
  font-weight: 600;
  line-height: 1.25;
  overflow-wrap: anywhere;
}

.profile-subtitle {
  margin: 6px 0 0;
  color: rgba(var(--v-theme-on-background), 0.7);
  font-size: 0.9375rem;
}

.profile-follow {
  flex-shrink: 0;
  text-transform: none;
  letter-spacing: normal;
}

@media (max-width: 599px) {
  .profile-header {
    padding: 16px 0 24px;
    gap: 16px;
  }
}
</style>
