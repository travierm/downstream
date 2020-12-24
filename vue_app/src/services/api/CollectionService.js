import http from "./Client"

export async function fetchUserCollection() {
    return http.post("/collection")
}