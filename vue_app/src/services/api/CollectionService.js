import API from './Client'

export async function fetchUserCollection() {
    return API.post("/collection");
}