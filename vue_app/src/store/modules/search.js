import { searchByQuery } from '../../services/api/SearchService';

export const namespaced = true
export const state = {
    query: ""
}

export const mutations = {
    SET_QUERY(state, query) {
        state.query = query
    }
}

export const getters = {}

export const actions = {
    byQuery(context, query) {
        context.commit('SET_QUERY', query)

        return searchByQuery(query)
    },
}
