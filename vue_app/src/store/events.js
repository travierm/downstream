import store from "./index"

export async function fetchInitUserData() {
    await store.dispatch("auth/getUser")

    if(store.state.auth.user) {    
        store.dispatch("collection/fetchUserCollection")
        store.dispatch("search/getAutocompleteData")
    }
}
