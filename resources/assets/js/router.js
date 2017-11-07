import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

//Vue Router Setup
const Foo = { template: '<div>foo</div>' }
const Bar = { template: '<div>bar</div>' }

const routes = [
  { path: '/foo', component: Foo },
  { path: '/collection', component: Bar }
]

export default new VueRouter({
  routes // short for `routes: routes`
})
