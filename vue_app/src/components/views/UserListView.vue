<template>
  <v-container class="users-view px-4 px-sm-6 py-6 py-sm-10">
    <header class="mb-8">
      <div class="text-label-small text-primary mb-2">The Downstream community</div>
      <h1 class="text-headline-large text-sm-display-medium font-weight-bold mb-3">Top Users</h1>
      <p class="text-body-large text-medium-emphasis users-intro">
        Find your next source of inspiration. Explore the community's biggest collections
        and the people behind them.
      </p>
      <v-chip variant="tonal" color="primary" size="small" class="mt-4">
        <v-icon icon="$mdi-playlist-music" start size="16" />
        Ranked by collection size
      </v-chip>
    </header>

    <v-row v-if="loading" aria-label="Loading community profiles" aria-busy="true">
      <v-col v-for="index in 6" :key="index" cols="12" sm="6" lg="4">
        <v-skeleton-loader type="avatar, heading, paragraph, actions" rounded="xl" />
      </v-col>
    </v-row>

    <v-alert v-else-if="error" type="error" variant="tonal" rounded="lg" role="alert">
      {{ error }}
      <template #append>
        <v-btn variant="text" @click="loadUsers">Try again</v-btn>
      </template>
    </v-alert>

    <v-sheet v-else-if="!users.length" rounded="xl" class="pa-8 text-center" border>
      <v-icon icon="$mdi-account-details" size="40" color="primary" class="mb-3" />
      <h2 class="text-title-large mb-2">A community in the making</h2>
      <p class="text-body-medium text-medium-emphasis mb-0">Public profiles will appear here as people join Downstream.</p>
    </v-sheet>

    <v-row v-else>
      <v-col v-for="(user, index) in users" :key="user.id" cols="12" sm="6" lg="4" class="d-flex">
        <v-card rounded="xl" variant="outlined" class="user-card d-flex flex-column w-100">
          <v-card-text class="pa-6 flex-grow-1">
            <div class="d-flex align-center ga-3 mb-5">
              <v-avatar size="56" color="primary" variant="tonal" rounded="lg">
                <v-img :src="avatar(user)" alt="" eager />
              </v-avatar>
              <div class="user-identity flex-grow-1">
                <h2 class="text-title-large font-weight-bold user-name">{{ user.display_name }}</h2>
                <div class="text-body-small text-medium-emphasis">Downstream collector</div>
              </div>
              <v-chip size="small" variant="tonal" :color="index === 0 ? 'primary' : undefined"
                :aria-label="`Collection rank ${index + 1}`" class="align-self-start">
                #{{ index + 1 }}
              </v-chip>
            </div>

            <div class="text-body-medium text-medium-emphasis mb-5">
              Joined <time v-if="user.created_at" :datetime="user.created_at">{{ joinedDate(user.created_at) }}</time>
              <span v-else>date unavailable</span>
            </div>

            <v-sheet rounded="lg" class="collection-stat pa-4 mb-4">
              <div class="d-flex align-center ga-2 text-medium-emphasis text-body-medium mb-2">
                <v-icon icon="$mdi-playlist-music" size="20" color="primary" />
                Collection size
              </div>
              <div class="text-headline-large font-weight-bold">{{ formatCount(user.media_count) }}</div>
              <div class="text-body-small text-medium-emphasis mt-1">Tracks collected</div>
            </v-sheet>

            <dl class="profile-stats">
              <div>
                <dt class="text-body-small text-medium-emphasis d-flex align-center ga-1">
                  <v-icon icon="$mdi-spotify" size="16" /> Spotify imports
                </dt>
                <dd class="text-title-large font-weight-bold mt-1">{{ formatCount(user.spotify_imports_count) }}</dd>
              </div>
              <div>
                <dt class="text-body-small text-medium-emphasis">Followers</dt>
                <dd class="text-title-large font-weight-bold mt-1">{{ formatCount(user.followers_count) }}</dd>
              </div>
            </dl>
          </v-card-text>
          <v-divider />
          <v-card-actions class="pa-4">
            <v-btn block variant="tonal" color="primary" rounded="lg" :to="'/profile/' + user.hash"
              :aria-label="`View ${user.display_name}'s profile`">
              View profile
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { identicon } from 'minidenticons'
import { getActiveUsers } from '@/services/api/FollowerService'

const countFormatter = new Intl.NumberFormat('en-US')
const dateFormatter = new Intl.DateTimeFormat('en-US', {
  month: 'long', day: 'numeric', year: 'numeric', timeZone: 'UTC',
})

export default {
  name: 'UserListView',
  data: () => ({
    users: [],
    loading: true,
    error: '',
  }),
  methods: {
    avatar(user) {
      return `data:image/svg+xml,${encodeURIComponent(identicon(user.display_name || user.hash))}`
    },
    formatCount(value) {
      return value == null ? '—' : countFormatter.format(value)
    },
    joinedDate(value) {
      const date = new Date(value)
      return Number.isNaN(date.getTime()) ? 'date unavailable' : dateFormatter.format(date)
    },
    async loadUsers() {
      this.loading = true
      this.error = ''
      try {
        const response = await getActiveUsers()
        this.users = response.data
      } catch {
        this.error = 'We couldn’t load the community. Please try again.'
      } finally {
        this.loading = false
      }
    },
  },
  mounted() {
    this.loadUsers()
  },
}
</script>

<style scoped>
.users-view {
  max-width: 1200px;
}

.users-intro {
  max-width: 560px;
}

.user-card {
  border-color: rgba(var(--v-theme-on-surface), 0.12);
  background: linear-gradient(150deg, rgba(var(--v-theme-primary), 0.06), transparent 55%), rgb(var(--v-theme-surface));
}

.user-identity {
  min-width: 0;
}

.user-name {
  overflow-wrap: anywhere;
  line-height: 1.4;
}

.collection-stat {
  background: rgba(var(--v-theme-primary), 0.08);
}

.profile-stats {
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
  gap: 16px;
}

.profile-stats > div + div {
  border-left: 1px solid rgba(var(--v-theme-on-surface), 0.12);
  padding-left: 16px;
}

.profile-stats dd {
  margin-left: 0;
}
</style>
