class LocalCache {
    constructor() {

    }

    set(key, value) {
        window.localStorage[key] = value;
    }

    get(key, defaultValue) {
        return window.localStorage[key] || defaultValue;
    }
}

let cache = new LocalCache();

export default cache;
