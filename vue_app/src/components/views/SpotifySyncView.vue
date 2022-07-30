<template>
  <v-container>
    <h1>
      <v-icon x-large class="mr-1 mb-2" color="green">{{ mdiSpotify }}</v-icon>
      Spotify Sync
    </h1>

    <h3 v-if="hasSpotifyConnection" class="green--text">
      Your Spotify Account is connected with Downstream!
    </h3>
    <h3 v-if="!hasSpotifyConnection" class="amber--text">
      Your Spotify Account is not connected with Downstream.
    </h3>

    <v-btn
      v-if="!hasSpotifyConnection"
      @click="getAuthorizeUrl()"
      class="mt-2 mb-2"
      depressed
      color="primary"
    >
      Connect Spotify Account
    </v-btn>

    <v-btn
      v-if="hasSpotifyConnection"
      @click="clickSyncNow"
      class="mt-2 mb-2"
      depressed
      color="success"
      :loading="syncing"
    >
      Sync Now
    </v-btn>

    <!-- Import Graph -->
    <apexchart
      ref="chart"
      class="mt-2"
      height="250"
      type="line"
      :options="chart.options"
      :series="chart.series"
    ></apexchart>

    <v-btn
      v-if="hasSpotifyConnection"
      @click="disableAccess()"
      class="mt-2"
      depressed
      color="warning"
    >
      Disconnect Spotify Account
    </v-btn>
    <ConfirmDialog
      message="Are you sure you want to disconnect your spotify account?"
      :show="showDisableConfirmation"
      @confirmed="disableAccess"
      @closed="showDisableConfirmation = false"
    />
  </v-container>
</template>

<script>
import { mapState } from 'vuex'
import { mdiSpotify } from '@mdi/js'
import BottomBar from '@/components/BottomBar'
import {
  getAuthorizeUrl,
  getDisable,
  getImportStats,
  runSpotifySync,
} from '../../services/api/spotify'
import ConfirmDialog from '../Shared/ConfirmDialog.vue'

const defaultOptions = {
  theme: {
    mode: 'dark',
    palette: 'palette4',
  },
  chart: {
    height: 350,
    type: 'line',
    zoom: {
      enabled: false,
    },
  },
  dataLabels: {
    enabled: false,
  },
  stroke: {
    curve: 'straight',
  },
  title: {
    text: 'Spotify Sync History',
    align: 'left',
  },
  grid: {
    row: {
      colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
      opacity: 0.2,
    },
  },
  xaxis: {
    categories: [],
  },
}

export default {
  name: 'SpotifySyncView',
  components: {
    BottomBar,
    ConfirmDialog,
  },
  data: () => ({
    mdiSpotify,
    chart: {
      series: [
        {
          name: 'Synced Items',
          data: [],
        },
      ],
      options: {
        ...defaultOptions,
      },
    },
    syncing: false,
    showDisableConfirmation: false,
  }),
  computed: {
    ...mapState({
      hasSpotifyConnection: (state) => state.auth.user.has_spotify_connection,
    }),
  },
  mounted() {
    this.fetchImportStats()
  },
  methods: {
    async clickSyncNow() {
      if (this.syncing) {
        return
      }

      try {
        this.syncing = true
        await runSpotifySync()
        await this.fetchImportStats()
      } catch (error) {
        console.error(error)
      } finally {
        this.syncing = false
      }
    },
    async fetchImportStats() {
      try {
        const resp = await getImportStats()

        this.chart.series[0].data = resp.data.data
        this.chart.options.xaxis = {
          categories: resp.data.categories,
        }

        this.$refs.chart.updateOptions({
          ...defaultOptions,
          xaxis: {
            categories: resp.data.categories,
          },
        })
      } catch (error) {
        console.error(error)
      }
    },

    async disableAccess(confirmed) {
      if (!confirmed) {
        this.showDisableConfirmation = true
        return
      }

      try {
        this.showDisableConfirmation = false
        await getDisable()
        this.$store.dispatch('auth/getUser')
        location.reload()
      } catch (error) {
        console.error(error)
      }
    },

    getAuthorizeUrl() {
      getAuthorizeUrl()
        .then((resp) => {
          window.open(resp.data, '_self')
        })
        .catch((err) => {
          throw err
        })
    },
  },
  watch: {},
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
