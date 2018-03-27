import Vue from 'vue';
import VueRouter from 'vue-router';

import CollectionPage from './pages/collection';
import SearchPage from './pages/search';
import UserProfile from './pages/user/profile';

Vue.use(VueRouter);

const routes = [
  { path: '/collection', component: CollectionPage },
  { path: '/search', component: SearchPage },
  { path: '/user/:hash/profile', component: UserProfile}
];

export default new VueRouter({
	mode: 'history',
	routes
});
