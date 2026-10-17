<template>
  <v-container fluid class="admin-page">
    <div class="d-flex align-center mb-4">
      <h2>Admin</h2>
      <v-spacer />
      <v-btn outlined :loading="loading" @click="loadDashboard">
        <v-icon left small>{{ icons.refresh }}</v-icon> Refresh
      </v-btn>
    </div>

    <v-alert v-if="error" type="error" outlined role="alert">{{ error }}</v-alert>
    <div v-if="loading && !dashboard" class="text-center py-12" role="status" aria-label="Loading statistics">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <template v-if="dashboard">
      <v-row aria-label="Statistics">
        <v-col v-for="card in statCards" :key="card.label" cols="12" sm="6" lg="3">
          <v-card class="fill-height">
            <v-card-text>
              <div class="text-subtitle-1">{{ card.label }}</div>
              <div class="text-h4 my-2 text--primary">{{ number(card.value) }}</div>
              <div class="text-caption">{{ card.detail }}</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <v-card class="mt-4" aria-labelledby="activity-title">
        <v-card-title class="d-flex flex-wrap align-center">
          <h3 id="activity-title" class="text-h6">Daily activity</h3>
          <v-spacer />
          <span class="text-caption text--secondary">Last 14 days<span v-if="dashboard.timezone"> · {{ dashboard.timezone }}</span></span>
        </v-card-title>
        <v-card-text>
          <div class="d-flex flex-wrap mb-2">
            <span class="mr-6"><strong>{{ number(activityTotals.collections) }}</strong> songs collected</span>
            <span><strong>{{ number(activityTotals.users) }}</strong> new users</span>
          </div>
          <apexchart type="line" height="300" :options="chartOptions" :series="chartSeries" />
        </v-card-text>
      </v-card>
      <div class="text-caption text--secondary mt-4">Updated {{ fullDate(dashboard.updated_at) }} · Last 7 days includes today</div>
    </template>
  </v-container>
</template>

<script>
import { mdiRefresh } from '@mdi/js'
import { getAdminDashboard } from '@/services/api/AdminService'

export default {
  name: 'AdminView',
  data: () => ({
    dashboard: null,
    loading: false,
    error: '',
    icons: { refresh: mdiRefresh },
  }),
  computed: {
    statCards() {
      const stats = this.dashboard.stats
      return [
        { label: 'Total users', value: stats.users, detail: `+${this.number(stats.new_users_week)} joined in the last 7 days` },
        { label: 'Songs collected', value: stats.collections, detail: `+${this.number(stats.collections_week)} collected in the last 7 days` },
        { label: 'Unique songs', value: stats.unique_songs, detail: 'Distinct songs in current collections' },
        { label: 'Plays this week', value: stats.plays_week, detail: `${this.number(stats.active_collectors_week)} active collectors in the last 7 days` },
      ]
    },
    activityTotals() {
      return this.dashboard.activity.reduce((total, day) => ({
        collections: total.collections + day.collections,
        users: total.users + day.users,
      }), { collections: 0, users: 0 })
    },
    chartSeries() {
      return [
        { name: 'Songs collected', data: this.dashboard.activity.map(day => day.collections) },
        { name: 'New users', data: this.dashboard.activity.map(day => day.users) },
      ]
    },
    chartOptions() {
      return {
        theme: { mode: 'dark', palette: 'palette4' },
        chart: { background: 'transparent', toolbar: { show: false }, zoom: { enabled: false } },
        colors: ['#00FFCA', '#24b6ff'],
        stroke: { curve: 'straight', width: 2 },
        dataLabels: { enabled: false },
        grid: { row: { colors: ['#f3f3f3', 'transparent'], opacity: 0.2 } },
        xaxis: { categories: this.dashboard.activity.map(day => day.date), labels: { rotate: -35 }, axisBorder: { show: false }, axisTicks: { show: false }, tickAmount: 6 },
        yaxis: { min: 0, forceNiceScale: true, labels: { formatter: value => this.number(value) } },
        tooltip: { theme: 'dark', y: { formatter: value => this.number(value) } },
        legend: { position: 'top', horizontalAlign: 'right' },
      }
    },
  },
  mounted() {
    this.loadDashboard()
  },
  methods: {
    async loadDashboard() {
      if (this.loading) return
      this.loading = true
      this.error = ''
      try {
        const response = await getAdminDashboard()
        this.dashboard = response.data
      } catch (error) {
        if (error?.status === 403 || error?.status === 401) {
          this.dashboard = null
          this.error = 'Administrator access is required to view this panel.'
        } else {
          this.error = 'Could not load the latest activity. Use Refresh to try again.'
        }
      } finally {
        this.loading = false
      }
    },
    number(value) { return new Intl.NumberFormat(undefined, { maximumFractionDigits: 0 }).format(value) },
    fullDate(value) { return value ? new Date(value).toLocaleString() : 'Unknown date' },
  },
}
</script>

<style scoped>
.admin-page { padding-bottom: 100px; }
</style>
