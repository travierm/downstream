
export default class Ajax {
  constructor(axios, token) {
    this.axios = axios;
    this.api_token = token;
  }

  get(url) {
    return this.axios.get(url);
  }

  post(url, params) {
    const data = {
      params
    }

    return this.axios.post(url, data);
  }
}
