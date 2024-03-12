import store from '@/store/index';
import axios from 'axios';

const http = axios.create({
  withCredentials: true,
  baseURL: import.meta.env.VITE_API_URL,
})

const viteMode = import.meta.env.MODE;

if (viteMode !== 'production') {
  console.log('vite_mode:', viteMode);
}

http.interceptors.request.use(
  function (config) {
    const token = window.localStorage.getItem('token')
    if (token != null) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  function (error) {
    return Promise.reject(error.response)
  }
)

http.interceptors.response.use(
  (response) => {
    return response
  },
  function (error) {
    if (error.response.status === 401) {
      store.dispatch('auth/logout')
    }
    return Promise.reject(error.response)
  }
)

export default http
