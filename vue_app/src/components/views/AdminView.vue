<template>
  <div class="admin-page">
    <header class="admin-header">
      <div>
        <div class="admin-eyebrow"><span class="status-dot"></span> DOWNSTREAM / ADMIN</div>
        <h1>The community, in motion.</h1>
        <p>A little pulse check on the people and music making downstream flow.</p>
      </div>
      <v-btn outlined rounded :loading="loading" @click="loadDashboard">
        <v-icon left small>{{ icons.refresh }}</v-icon> Refresh
      </v-btn>
    </header>

    <v-alert v-if="error" type="error" outlined role="alert">{{ error }}</v-alert>
    <div v-if="loading && !dashboard" class="admin-loading" role="status">
      <v-progress-circular indeterminate color="primary" />
      <p>Gathering the latest activity…</p>
    </div>

    <template v-if="dashboard">
      <section class="stat-grid" aria-label="Community statistics">
        <article v-for="card in statCards" :key="card.label" class="stat-card" :style="{ '--stat-color': card.color }">
          <div class="stat-top"><span>{{ card.label }}</span><v-icon :color="card.color">{{ card.icon }}</v-icon></div>
          <div class="stat-value">{{ number(card.value) }}</div>
          <div class="stat-detail">{{ card.detail }}</div>
        </article>
      </section>

      <section class="admin-panel activity-panel" aria-labelledby="activity-title">
        <div class="panel-heading">
          <div><span class="admin-eyebrow">THE BIG PICTURE</span><h2 id="activity-title">Good things are adding up.</h2></div>
          <span class="period-pill">Last 14 days · {{ dashboard.timezone }}</span>
        </div>
        <div class="activity-summary">
          <span><b>{{ number(activityTotals.collections) }}</b> songs collected</span>
          <span><b>{{ number(activityTotals.users) }}</b> people joined</span>
        </div>
        <apexchart type="area" height="260" :options="chartOptions" :series="chartSeries" />
      </section>

      <div class="admin-feeds">
        <section class="admin-panel" aria-labelledby="users-title">
          <div class="panel-heading"><div><span class="admin-eyebrow">FRESH FACES</span><h2 id="users-title">Recently joined</h2></div><span class="feed-count">Latest {{ dashboard.recent_users.length }}</span></div>
          <p v-if="!dashboard.recent_users.length" class="empty-feed">The first chapter starts with a new listener. New users will appear here.</p>
          <ul v-else class="activity-list">
            <li v-for="user in dashboard.recent_users" :key="user.id" class="feed-row">
              <div class="user-avatar" aria-hidden="true">{{ initials(user.display_name) }}</div>
              <div class="feed-info"><div class="feed-title">{{ user.display_name || 'Unnamed listener' }} <span v-if="user.type === 'admin'" class="admin-badge">ADMIN</span></div><div class="feed-meta">{{ number(user.collection_count) }} songs in collection</div></div>
              <time :datetime="user.created_at" :title="fullDate(user.created_at)">{{ shortDate(user.created_at) }}</time>
            </li>
          </ul>
        </section>

        <section class="admin-panel" aria-labelledby="songs-title">
          <div class="panel-heading"><div><span class="admin-eyebrow">ON REPEAT</span><h2 id="songs-title">Recently collected</h2></div><v-icon color="#70dfc1">{{ icons.music }}</v-icon></div>
          <p v-if="!dashboard.recent_collections.length" class="empty-feed">Waiting for the first find. Collected songs will show up here.</p>
          <ul v-else class="activity-list">
            <li v-for="song in dashboard.recent_collections" :key="song.id" class="feed-row">
              <v-img v-if="song.thumbnail" :src="song.thumbnail" width="44" height="44" max-width="44" class="song-art" alt="">
                <template v-slot:placeholder><v-icon>{{ icons.music }}</v-icon></template>
              </v-img>
              <div v-else class="song-placeholder" aria-hidden="true"><v-icon>{{ icons.music }}</v-icon></div>
              <div class="feed-info"><div class="feed-title" :title="song.title">{{ song.title || 'Untitled song' }}</div><div class="feed-meta">Collected by {{ song.collected_by || 'Unnamed listener' }}</div></div>
              <time :datetime="song.created_at" :title="fullDate(song.created_at)">{{ shortDate(song.created_at) }}</time>
            </li>
          </ul>
        </section>
      </div>
      <footer class="admin-footer"><span class="status-dot"></span> Updated {{ fullDate(dashboard.updated_at) }} · Last 7 days includes today</footer>
    </template>
  </div>
</template>

<script>
import { mdiRefresh, mdiAccountGroupOutline, mdiMusicNoteOutline, mdiPlaylistMusicOutline, mdiPlayCircleOutline } from '@mdi/js'
import { getAdminDashboard } from '@/services/api/AdminService'

export default {
  name: 'AdminView',
  data: () => ({
    dashboard: null,
    loading: false,
    error: '',
    icons: { refresh: mdiRefresh, music: mdiMusicNoteOutline },
  }),
  computed: {
    statCards() {
      const stats = this.dashboard.stats
      return [
        { label: 'Total listeners', value: stats.users, detail: `+${this.number(stats.new_users_week)} joined in the last 7 days`, icon: mdiAccountGroupOutline, color: '#bca6ff' },
        { label: 'Songs collected', value: stats.collections, detail: `+${this.number(stats.collections_week)} collected in the last 7 days`, icon: mdiPlaylistMusicOutline, color: '#70dfc1' },
        { label: 'Unique songs', value: stats.unique_songs, detail: 'Distinct songs in current collections', icon: mdiMusicNoteOutline, color: '#ffcb85' },
        { label: 'Plays this week', value: stats.plays_week, detail: `${this.number(stats.active_collectors_week)} people collecting this week`, icon: mdiPlayCircleOutline, color: '#86c8ff' },
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
        { name: 'New listeners', data: this.dashboard.activity.map(day => day.users) },
      ]
    },
    chartOptions() {
      return {
        theme: { mode: 'dark' },
        chart: { background: 'transparent', foreColor: '#a3a2b6', toolbar: { show: false }, zoom: { enabled: false } },
        colors: ['#70dfc1', '#bca6ff'],
        stroke: { curve: 'smooth', width: 3 },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.28, opacityTo: 0.01 } },
        dataLabels: { enabled: false },
        grid: { borderColor: '#ffffff0d', strokeDashArray: 4 },
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
    initials(name) { return (name || '?').trim().split(/\s+/).slice(0, 2).map(word => word.charAt(0)).join('').toUpperCase() },
    shortDate(value) { return value ? new Date(value).toLocaleDateString(undefined, { month: 'short', day: 'numeric' }) : 'Unknown date' },
    fullDate(value) { return value ? new Date(value).toLocaleString() : 'Unknown date' },
  },
}
</script>

<style scoped>
.admin-page { max-width: 1480px; margin: 0 auto; padding: 42px 36px 100px; color: #f5f3ff; }
.admin-header { display: flex; justify-content: space-between; align-items: center; gap: 24px; margin-bottom: 32px; }
.admin-eyebrow { color: #aaa3bf; font-size: 10px; font-weight: 700; letter-spacing: 2px; }
.status-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #70dfc1; margin-right: 7px; box-shadow: 0 0 12px #70dfc144; }
h1 { font-size: clamp(28px, 3vw, 42px); font-weight: 700; letter-spacing: -1.4px; margin: 10px 0 8px; line-height: 1.2; }
.admin-header p { color: #aaa3bf; margin: 0; font-size: 14px; }
.stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; margin-bottom: 24px; }
.stat-card { padding: 23px; border: 1px solid #ffffff12; border-radius: 18px; background: linear-gradient(135deg, #262333, #1b1b23); position: relative; overflow: hidden; }
.stat-card::after { content: ''; position: absolute; right: -35px; top: -45px; width: 100px; height: 100px; background: var(--stat-color); filter: blur(60px); opacity: 0.12; pointer-events: none; }
.stat-top { display: flex; justify-content: space-between; align-items: center; gap: 8px; font-size: 13px; color: #c5bfd6; }
.stat-value { font-size: 38px; font-weight: 700; letter-spacing: -1px; margin: 15px 0 6px; line-height: 1.1; }
.stat-detail { font-size: 11px; color: var(--stat-color); }
.admin-panel { background: #1b1b23; border: 1px solid #ffffff12; border-radius: 18px; overflow: hidden; min-width: 0; }
.activity-panel { margin-bottom: 24px; padding: 24px 24px 8px; background: radial-gradient(ellipse at top left, #30254066, transparent 65%), #1b1b23; }
.panel-heading { padding: 24px 24px 20px; display: flex; justify-content: space-between; align-items: center; gap: 12px; }
.activity-panel .panel-heading { padding: 0; }
h2 { font-size: 19px; letter-spacing: -0.4px; font-weight: 600; margin: 6px 0 0; }
.period-pill { padding: 7px 12px; border: 1px solid #ffffff1a; border-radius: 30px; font-size: 11px; white-space: nowrap; color: #bbb5cf; }
.activity-summary { display: flex; flex-wrap: wrap; gap: 24px; margin-top: 22px; color: #aaa3bf; font-size: 12px; }
.activity-summary b { color: #f5f3ff; font-size: 19px; margin-right: 5px; }
.admin-feeds { display: grid; grid-template-columns: 1fr 1.2fr; gap: 24px; }
.feed-count { color: #aaa3bf; font-size: 11px; white-space: nowrap; }
.activity-list { list-style: none; padding: 0; margin: 0; }
.feed-row { display: flex; align-items: center; gap: 12px; padding: 15px 24px; border-top: 1px solid #ffffff0a; }
.feed-row:hover { background: #ffffff03; }
.user-avatar, .song-placeholder { width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #bca6ff14; color: #c4b0ff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; }
.song-art { border-radius: 8px; background: #ffffff08; }
.song-placeholder { border-radius: 8px; background: #70dfc112; }
.feed-info { min-width: 0; flex: 1; }
.feed-title { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 13px; font-weight: 500; }
.feed-meta { color: #aaa3bf; font-size: 11px; margin-top: 4px; overflow-wrap: anywhere; }
.feed-row time { color: #aaa3bf; font-size: 10px; white-space: nowrap; }
.admin-badge { display: inline-block; vertical-align: middle; font-size: 8px; letter-spacing: 0.5px; color: #c4b0ff; background: #bca6ff16; padding: 2px 5px; margin-left: 4px; border-radius: 4px; }
.empty-feed { padding: 24px; color: #aaa3bf; font-size: 13px; }
.admin-footer { padding-top: 22px; color: #aaa3bf; font-size: 11px; }
.admin-loading { text-align: center; padding: 100px 16px; color: #aaa3bf; }
@media (max-width: 1100px) { .stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .admin-feeds { grid-template-columns: 1fr; } }
@media (max-width: 600px) { .admin-page { padding: 24px 16px 100px; } .admin-header { align-items: flex-start; flex-direction: column; } .stat-grid { gap: 10px; } .stat-card { padding: 16px; } .stat-value { font-size: 30px; } .stat-top { font-size: 11px; } .stat-detail { line-height: 1.6; } .activity-panel { padding: 18px 12px 8px; } .panel-heading { padding: 20px 16px; flex-wrap: wrap; } .feed-row { padding: 14px 16px; } h2 { font-size: 17px; } .period-pill { font-size: 10px; } }
</style>
