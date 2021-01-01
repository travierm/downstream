import http from "./Client"

async function fetchUserCollection() {
    return http.get("/collection")
}

async function collectItem(videoId) {
    return http.post("/media/collect", {
        videoId,
    })
}

async function removeItem(itemId) {
    return http.delete(`/media/collection/${itemId}`)
}

export default { collectItem, removeItem, fetchUserCollection }
