import Vue from 'vue';
import VueRouter from 'vue-router';

import Collection from './pages/collection';
// import SearchPage from './pages/search';
import DiscoverPage from './pages/discover';
import UserProfile from './pages/user/profile';
import RadioPage from './pages/radio';
import ListIndex from './pages/lists/index';
import ListCreate from './pages/lists/create';
import MediaEdit from './pages/media/edit';
import DemoIndex from './pages/demo/index';

// Disabled for now
// import StreamIndex from './pages/stream/index';

Vue.use(VueRouter);

/*
  Single Page Router
  each route must be named to separate Laravel paths from vue-router paths
*/
const routes = [
  {
    name: 'discover',
    path: '/discover',
    component: DiscoverPage,
  },
  {
    name: 'demo-index',
    path: '/demo',
    component: DemoIndex,
  },
  {
    name: 'collection',
    path: '/collection/:filter?',
    component: Collection,
  },
  {
    name: 'listIndex',
    path: '/lists',
    component: ListIndex,
  },
  {
    name: 'listsCreate',
    path: '/list/create',
    component: ListCreate,
  },
  {
    name: 'radioIndex',
    path: '/radio',
    component: RadioPage,
  },
  {
    name: 'userProfile',
    path: '/user/:hash/profile',
    component: UserProfile,
  },
  {
    name: 'mediaEdit',
    path: '/media/edit/:id',
    component: MediaEdit,
    props: { id: false },
  },
];

export default new VueRouter({
  mode: 'history',
  routes,
});
