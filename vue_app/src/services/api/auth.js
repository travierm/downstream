import axios from 'axios'

const apiHost = 'http://localhost:8000'

class Auth {
    constructor() {

    }

    combineRoute(routeName) {
        return apiHost + routeName
    }

    initCSRF() {
        const route = this.combineRoute('/sanctum/csrf-cookie')

        axios.get(route).then((response) => {
            console.log(response)
        })
    }
}

const auth = new Auth()

export default auth