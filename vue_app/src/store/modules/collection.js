import { fetchUserCollection } from '@/services/api/CollectionService';

export const namespaced = true;
export const state = {
    userCollection: false
}

export const mutations = {
    /*
    SET_EXAMPLE(state, example) {
        state.example = example;
    },
    */
   SET_USER_COLLECTION(state, items) {
       state.userCollection = Object.freeze(items)
   }
}

export const getters = {

}

export const actions = {
    async fetchUserCollection({ commit, rootState, state }) {

        if(!rootState.auth.token || state.userCollection) {
            return;
        }

        let response = await fetchUserCollection()
        
        if(response.data) {
            commit("SET_USER_COLLECTION", response.data.items)
        }
    }
}