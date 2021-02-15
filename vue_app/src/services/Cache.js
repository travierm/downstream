export default class Cache {
  constructor(useStorage = true) {
    this.store = {}
    this.prefix = ''
    this.useStorage = useStorage
    this.storage = window.localStorage
  }

  setStoragePrefix(prefix) {
    this.prefix = prefix
  }

  addKeyPrefix(key) {
    return this.prefix + key
  }

  get(key, defaultValue = false) {
    key = this.addKeyPrefix(key)

    if (this.store[key]) {
      return this.store[key]
    }

    const storageValue = this.storage.getItem(key)
    if (storageValue && this.useStorage) {
      return storageValue
    }

    return defaultValue
  }

  set(key, value) {
    key = this.addKeyPrefix(key)

    this.store[key] = value

    if (this.useStorage) {
      this.storage.setItem(key, value)
    }
  }
}
