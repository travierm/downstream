import API from './Client';

export default {
    login(params) {
        return API.post("/auth/login", params);
    },
    logout() {
        return API.post("/auth/logout")
    }
}