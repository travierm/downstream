import packageJson from '../../package.json';

/**
 * Global Functions
 *
 * Functions that should be available to all scripts
 */

// Generate a unique element ID that won't cause conflicts with querying the dom
export function generateElementId() {
  return 'egen_' + Math.random().toString(36).substr(2, 9)
}

export function getAppVersion() {
  return packageJson.version
}

export function setUrlParam(param, value) {
  let currentUrl = new URL(window.location.href)

  currentUrl.searchParams.set(param, value)

  let newUrl = currentUrl.toString()
  window.history.replaceState({}, document.title, newUrl)
}

export function deleteUrlParam(param) {
  let currentUrl = new URL(window.location.href)

  currentUrl.searchParams.delete(param)

  let newUrl = currentUrl.toString()
  window.history.replaceState({}, document.title, newUrl)
}

export function getUrlParam(param) {
  let currentUrl = new URL(window.location.href)

  return currentUrl.searchParams.get(param)
}