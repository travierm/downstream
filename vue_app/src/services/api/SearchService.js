import API from './Client'

export async function getAutocompleteData() {
    return API.get("/search/autocomplete");
}