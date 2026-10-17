import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import { aliases, mdi } from 'vuetify/iconsets/mdi-svg'
import {
  mdiAccountDetails,
  mdiAccountMultiplePlus,
  mdiAlbum,
  mdiBellAlertOutline,
  mdiChartBarStacked,
  mdiDatabase,
  mdiFullscreen,
  mdiGithub,
  mdiMagnify,
  mdiMusicNote,
  mdiPlaylistMusic,
  mdiShuffleDisabled,
  mdiShuffleVariant,
  mdiSpotify,
  mdiStarCircle,
  mdiVolumeHigh,
  mdiVolumeVariantOff,
} from '@mdi/js'

export default createVuetify({
  display: {
    // Keep runtime display checks aligned with the legacy grid in vuetify.scss.
    thresholds: { xs: 0, sm: 600, md: 960, lg: 1264, xl: 1904, xxl: 2560 },
  },
  theme: {
    defaultTheme: 'dark',
    themes: {
      dark: {
        dark: true,
        colors: {
          background: '#121212',
          surface: '#1e1e1e',
          'app-bar': '#272727',
          primary: '#7C4DFF',
          secondary: '#b0bec5',
          accent: '#8c9eff',
          error: '#b71c1c',
        },
      },
    },
  },
  icons: {
    defaultSet: 'mdi',
    aliases: {
      ...aliases,
      'mdi-account-details': mdiAccountDetails,
      'mdi-account-multiple-plus': mdiAccountMultiplePlus,
      'mdi-album': mdiAlbum,
      'mdi-bell-alert-outline': mdiBellAlertOutline,
      'mdi-chart-bar-stacked': mdiChartBarStacked,
      'mdi-database': mdiDatabase,
      'mdi-fullscreen': mdiFullscreen,
      'mdi-github': mdiGithub,
      'mdi-magnify': mdiMagnify,
      'mdi-music-note': mdiMusicNote,
      'mdi-playlist-music': mdiPlaylistMusic,
      'mdi-shuffle-disabled': mdiShuffleDisabled,
      'mdi-shuffle-variant': mdiShuffleVariant,
      'mdi-spotify': mdiSpotify,
      'mdi-star-circle': mdiStarCircle,
      'mdi-volume-high': mdiVolumeHigh,
      'mdi-volume-variant-off': mdiVolumeVariantOff,
    },
    sets: { mdi },
  },
  defaults: {
    VBtn: { variant: 'elevated' },
    VCard: { rounded: 'sm' },
  },
})
