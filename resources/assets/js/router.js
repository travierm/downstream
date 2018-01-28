import Vue from 'vue';
import VueRouter from 'vue-router';

import CollectionPage from './pages/collection';
import SearchPage from './pages/search';

Vue.use(VueRouter);

const routes = [
  { path: '/collection', component: CollectionPage },
  { path: '/search', component: SearchPage }
];

export default new VueRouter({
  routes
});
