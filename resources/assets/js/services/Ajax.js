
export default class Ajax {
  constructor(axios, token) {
    this.axios = axios;
    this.api_token = token;
  }

  post(url, data) {
    data['api_token'] = this.api_token;
    
    return this.axios.post(url, data);
  }
}
