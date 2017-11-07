import Vue from 'vue';
import Vuex from 'vuex'

Vue.use(Vuex)

const state = {
  playing:false
}

export default new Vuex.Store({
  state
})
