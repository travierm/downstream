import Utils from './services/Utils.js';
import MobileDetect from 'mobile-detect';
window._ = require('lodash');
window._utils = new Utils;

const md = new MobileDetect(window.navigator.userAgent);
window._isMobile = md.mobile();
window._authed = false;
/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */


try {
  window.$ = window.jQuery = require('jquery');

  window.Tether = require('tether');

  window.Popper = require('popper.js');
  require('bootstrap');
} catch (e) {
  console.log(e);
}

window.share = function(index) {
  const link = window.config.APP_LINK_URL + "/v/" + index;

  copyToClipboard(link);
}

window.copyToClipboard = function(text) {
    window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

const token = document.head.querySelector('meta[name="csrf-token"]');
const apiToken = document.head.querySelector('meta[name="xyz-token"]');

if (apiToken) {
  window.axios.defaults.headers.common.Authorization = `Bearer ${apiToken.content}`;
}

if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
  console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });
