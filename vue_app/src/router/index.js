import Vue from "vue"
import VueRouter from "vue-router"

import store from "@/store/index"
import Activity from "@/views/Activity.vue"
import SearchView from "@/views/SearchView"
import LoginView from "@/views/Auth/LoginView"
import LandingView from '@/views/LandingView'
import CollectionView from "@/views/CollectionView"

import { applyMiddleware } from "./middleware"

Vue.use(VueRouter)

const routes = [
    // Default Route
    {
        path: "/",
        component: LandingView,
    },
    {
        path: "/activity",
        name: "Activity",
        component: Activity,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/collection",
        name: "Collection",
        component: CollectionView,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/search",
        name: "Search",
        component: SearchView,
        meta: {
            requiresAuth: true,
        },
    },

    // Auth Views
    {
        path: "/login",
        name: "Login",
        component: LoginView,
    },
    {
        path: "/logout",
        name: "Logout",
        component: LoginView,
        beforeEnter(to, from, next) {
            store.dispatch("auth/logout")
            router.push("/")
        },
    },
]

const router = new VueRouter({
    mode: "history",
    base: process.env.BASE_URL,
    routes,
})

applyMiddleware(router)

export default router
