import Vue from 'vue';
import VueRouter from 'vue-router';
import CollectionPage from './pages/collection';
import FrontPage from './pages/frontpage';

Vue.use(VueRouter);

const routes = [
  { path: '/collection', component: CollectionPage },
  { path: '/frontpage', component: FrontPage }
];

export default new VueRouter({
  routes
});
