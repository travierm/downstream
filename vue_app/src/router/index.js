import Vue from 'vue'
import VueRouter from 'vue-router'

import store from '@/store/index'
import Activity from '@/components/views/Activity'
import SearchView from '@/components/views/SearchView'
import LoginView from '@/components/views/Auth/LoginView'
import LandingView from '@/components/views/LandingView'
import CollectionView from '@/components/views/CollectionView'
import DiscoverTrackView from '@/components/views/DiscoverTrackView'
import FollowingView from '@/components/views/FollowingView'

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
    path: '/activity',
    name: 'Activity',
    component: Activity,
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
    path: '/discover/track/:videoId',
    name: 'DiscoverTrack',
    component: DiscoverTrackView,
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
  {
    path: '/following',
    name: 'Following',
    component: FollowingView,
    meta: {
      requiresAuth: true,
    },
  },

  // Auth Views
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
  },
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
