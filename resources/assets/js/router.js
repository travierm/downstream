import Vue from 'vue';
import VueRouter from 'vue-router';

import CollectionPage from './pages/collection';
//import SearchPage from './pages/search';
import DiscoverPage from './pages/discover';
import UserProfile from './pages/user/profile';

import PlaylistIndex from './pages/playlists/index';
import PlaylistCreate from './pages/playlists/create';

Vue.use(VueRouter);

/*
	Single Page Router

	each route must be named to seperate laravel paths from vue-router paths	
*/
const routes = [
  { 
  	name:'discover',
  	path: '/discover', 
  	component: DiscoverPage 
  },
  { 
  	name:'collection',
  	path: '/collection', 
  	component: CollectionPage 
  },
  /*{ 
  	name:'search',
  	path: '/search', 
  	component: SearchPage 
  },*/
  {
	name: 'playlistIndex',
	path: '/playlists',
	component: PlaylistIndex
  },
  {
	name: 'playlistCreate',
	path: '/playlists/create',
	component: PlaylistCreate
  },
  { 
  	name:'userProfile',
  	path: '/user/:hash/profile', 
  	component: UserProfile
  }
];

export default new VueRouter({
	mode: 'history',
	routes
});
