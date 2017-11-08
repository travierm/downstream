import Vue from 'vue';
import VueRouter from 'vue-router';
import CollectionPage from './pages/collection';

Vue.use(VueRouter);

const routes = [
  { path: '/collection', component: CollectionPage }
];

export default new VueRouter({
  routes
});
