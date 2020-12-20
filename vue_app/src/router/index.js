import Vue from 'vue'
import VueRouter from 'vue-router'

import store from '@/store/index'

import Activity from '@/components/Pages/Activity.vue';
import SearchPage from '@/components/Pages/SearchPage';
import LoginPage from '@/components/Pages/Auth/LoginPage'
import CollectionPage from '@/components/Pages/CollectionPage'

Vue.use(VueRouter)

const routes = [
  // Default Route
  {
    path: '/',
    component: CollectionPage
  },
  {
    path: '/activity',
    name: 'Activity',
    component: Activity
  },
  {
    path: '/collection',
    name: 'Collection',
    component: CollectionPage
  },
  {
    path: '/search',
    name: 'Search',
    component: SearchPage
  },
  
  // Auth Pages
  {
    path: "/login",
    name: "Login",
    component: LoginPage
  },
  {
    path: "/logout",
    name: "Logout",
    component: LoginPage,
    beforeEnter(to, from, next) {
      store.dispatch('auth/logout')
      router.push('/login')
    }
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
