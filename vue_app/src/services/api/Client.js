import axios from "axios";
import store from "@/store/index";

const http = axios.create({
    baseURL: 'http://localhost:8000/api',
    withCredentials: true
});

http.interceptors.request.use(
    function(config) {
        const token = window.localStorage.getItem("token")
        if (token != null) {
            config.headers.Authorization = `Bearer ${token}`
        }
        return config
    },
    function(error) {
        return Promise.reject(error.response)
    }
)

http.interceptors.response.use(
    (response) => {
        return response
    },
    function(error) {
        if (error.response.status === 401) {
            store.dispatch("auth/logout")
        }
        return Promise.reject(error.response)
    }
)

export default http