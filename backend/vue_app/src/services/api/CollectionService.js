import API from './Client'

const apiLink = "http://localhost:8000";

export async function fetchUserCollection() {
    return API.post("/api/collection");
}