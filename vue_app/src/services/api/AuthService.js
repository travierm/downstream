import http from "./Client"

export default {
    login(params) {
        return http.post("/auth/login", params)
    },
    logout() {
        return http.post("/auth/logout")
    },
    getUser() {
        return http.get("/auth/user")
    },
}
