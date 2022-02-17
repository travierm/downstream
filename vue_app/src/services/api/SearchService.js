import { ajax } from "jquery"
import http from "./Client"

const suggestApiURL = "https://suggestqueries.google.com/complete/search?hl=en&ds=yt&client=youtube&hjson=t&cp=1&q="

export function getAutocompleteResults(query, callback) {
    ajax({
        type: "POST",
        url: suggestApiURL + query,
        dataType: "jsonp",
        success: function(data) {
            let items = []
            const responseItems = data[1]

            if(responseItems) {
                items = responseItems.map((item) => {
                    return item[0]
                })
            }

            callback(items)
        },
    })
}

export function searchByQuery(query) {
    return http.get("/search/" + query)
}
