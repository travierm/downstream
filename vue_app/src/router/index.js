import Vue from "vue"
import VueRouter from "vue-router"

import store from "@/store/index"

import Activity from "@/views/Activity.vue"
import SearchView from "@/views/SearchView"
import LoginView from "@/views/Auth/LoginView"
import CollectionView from "@/views/CollectionView"

Vue.use(VueRouter)

const routes = [
    // Default Route
    {
        path: "/",
        component: CollectionView,
    },
    {
        path: "/activity",
        name: "Activity",
        component: Activity,
    },
    {
        path: "/collection",
        name: "Collection",
        component: CollectionView,
    },
    {
        path: "/search",
        name: "Search",
        component: SearchView,
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
            router.push("/login")
        },
    },
]

const router = new VueRouter({
    mode: "history",
    base: process.env.BASE_URL,
    routes,
})

export default router
