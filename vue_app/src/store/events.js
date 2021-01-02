import store from "./index"

export async function fetchInitUserData() {
    store.dispatch("collection/fetchUserCollection")
}
