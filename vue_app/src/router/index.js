import AccountStatsView from '@/components/views/AccountStatsView'
import LoginView from '@/components/views/Auth/LoginView'
import CollectionView from '@/components/views/CollectionView'
import DailyMixView from '@/components/views/DailyMixView'
import DiscoverTrackView from '@/components/views/DiscoverTrackView'
import FollowerActivityView from '@/components/views/FollowerActivityView'
import InviteView from '@/components/views/InviteView'
import LandingView from '@/components/views/LandingView'
import ProfileView from '@/components/views/ProfileView'
import SearchView from '@/components/views/SearchView'
import SpotifySyncView from '@/components/views/SpotifySyncView'
import UserListView from '@/components/views/UserListView'
import store from '@/store/index'
import Vue from 'vue'
import VueRouter from 'vue-router'

import { connectSpotify } from '../services/api/spotify'
import { applyMiddleware } from './middleware'

Vue.use(VueRouter)

const routes = [
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
    beforeEnter(to, from, next) {
      console.log(to)

      connectSpotify(to.query.code, to.query.state).finally(() => {
        router.push('/spotify')
      })
    },
  },

  // Auth Views
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
  },
  // {
  //   path: '/waitlist',
  //   name: 'Wait List',
  //   component: WaitListView,
  // },
  // {
  //   path: '/register',
  //   name: 'Register',
  //   component: RegisterView,
  // },
  {
    path: '/logout',
    name: 'Logout',
    component: LoginView,
    beforeEnter(to, from, next) {
      store.dispatch('auth/logout')
      router.push('/')
    },
  },
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes,
})

applyMiddleware(router)

export default router
