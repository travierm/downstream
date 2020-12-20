import store from './index';

export function fetchInitUserData() {
    store.dispatch('auth/getUser')
    store.dispatch('collection/fetchUserCollection')
    store.dispatch('search/getAutocompleteData')
}