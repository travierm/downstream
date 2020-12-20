import axios from "axios";
import store from "@/store/index";

let apiClient = axios.create({
    baseURL: 'http://localhost:8001/api',
    withCredentials: true
});

apiClient.interceptors.request.use(
    function (config) {
        const token = window.localStorage.getItem("token");
        if (token != null) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    function (error) {
        return Promise.reject(error.response);
    }
);

apiClient.interceptors.response.use(
    response => {
        return response;
    },
    function (error) {
        if (error.response.status === 401) {
            store.dispatch("auth/logout");
        }
        return Promise.reject(error.response);
    }
);

export default apiClient