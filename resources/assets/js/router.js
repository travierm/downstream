import Vue from 'vue';
import VueRouter from 'vue-router';
import CollectionPage from './pages/collection';
import FrontPage from './pages/frontpage';
import TheaterPage from './pages/theater';

Vue.use(VueRouter);

const routes = [
  { name:'theater', path: '/theater/:mediaId', component: TheaterPage },
  { path: '/collection', component: CollectionPage },
  { path: '/frontpage', component: FrontPage },
];

export default new VueRouter({
  routes
});
