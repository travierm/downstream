import Vue from 'vue'
import VueRouter from 'vue-router'

import store from '@/store/index'

import Activity from '@/views/Activity.vue';
import SearchPage from '@/views/SearchPage';
import LoginPage from "@/views/Auth/LoginPage";
import CollectionPage from "@/views/CollectionPage";

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
