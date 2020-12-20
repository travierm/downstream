import { getAutocompleteData } from '@/services/api/SearchService';

export const namespaced = true;
export const state = {
    autocomplete: []
}

export const mutations = {
    
    SET_AUTOCOMPLETE_DATA(state, data) {
        state.autocomplete = data;
    },
}

export const getters = {

}

export const actions = {
    async getAutocompleteData({ commit }) {
        const response = await getAutocompleteData()

        if(response.data) {
            commit('SET_AUTOCOMPLETE_DATA', response.data)
        }
    }
}