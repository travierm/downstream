import { createRouter, createWebHistory } from 'vue-router'
import store from '@/store/index'
import { connectSpotify } from '../services/api/spotify'
import { applyMiddleware } from './middleware'

const AccountStatsView = () => import('@/components/views/AccountStatsView.vue')
const LoginView = () => import('@/components/views/Auth/LoginView.vue')
const CollectionView = () => import('@/components/views/CollectionView.vue')
const DailyMixView = () => import('@/components/views/DailyMixView.vue')
const DiscoverTrackView = () => import('@/components/views/DiscoverTrackView.vue')
const FollowerActivityView = () => import('@/components/views/FollowerActivityView.vue')
const InviteView = () => import('@/components/views/InviteView.vue')
const LandingView = () => import('@/components/views/LandingView.vue')
const ProfileView = () => import('@/components/views/ProfileView.vue')
const SearchView = () => import('@/components/views/SearchView.vue')
const RegisterView = () => import('@/components/views/Auth/RegisterView.vue')
const SpotifySyncView = () => import('@/components/views/SpotifySyncView.vue')
const UserListView = () => import('@/components/views/UserListView.vue')
const PrivacyView = () => import('@/components/views/PrivacyView.vue')
const TermsView = () => import('@/components/views/TermsView.vue')

const routes = [
  {
    path: '/admin',
    name: 'Admin',
    component: () => import('@/components/views/AdminView.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  // Default Route
  {
    path: '/',
    component: LandingView,
  },

  // In App Routes
  {
    path: '/follower/activity',
    name: 'Follower Activity',
    component: FollowerActivityView,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: '/users',
    name: 'User List',
    component: UserListView,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: '/invite',
    name: 'Invite Friend',
    component: InviteView,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: '/collection',
    name: 'Collection',
    component: CollectionView,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: '/stats',
    name: 'Account Stats',
    component: AccountStatsView,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: '/discover/track/:videoId',
    name: 'DiscoverTrack',
    component: DiscoverTrackView,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: '/daily-mix',
    name: 'DailyMix',
    component: DailyMixView,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: '/profile/:profileId',
    name: 'Profile',
    component: ProfileView,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: '/search',
    name: 'Search',
    component: SearchView,
    meta: {
      requiresAuth: true,
    },
  },

  // Spotify Views
  {
    path: '/spotify',
    name: 'Spotify',
    component: SpotifySyncView,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: '/spotify/connect',
    name: 'Spotify Connect',
    meta: {
      requiresAuth: true,
    },
    async beforeEnter(to) {
      try {
        await connectSpotify(to.query.code, to.query.state)
      } finally {
        return '/spotify'
      }
    },
  },

  {
    path: '/privacy',
    name: 'Privacy',
    component: PrivacyView
  },
  {
    path: '/tos',
    name: 'Terms',
    component: TermsView
  },

  // Auth Views
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
  },
  {
    path: '/waitlist',
    redirect: '/register',
  },
  {
    path: '/register',
    name: 'Register',
    component: RegisterView,
  },
  {
    path: '/logout',
    name: 'Logout',
    component: LoginView,
    async beforeEnter() {
      await store.dispatch('auth/logout')
      return '/'
    },
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

applyMiddleware(router)

export default router
