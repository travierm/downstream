import axios from "axios";

const apiLink = "http://localhost:8000";

export async function fetchUserCollection() {
    return axios.post(apiLink + '/api/collection');
}