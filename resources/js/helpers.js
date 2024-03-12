export function onRouteLoad(pathName, callback = () => {}) {
    window.addEventListener('popstate', function(event) {
        if (window.location.pathname === pathName) {
            callback()
        }
    });

    if (window.location.pathname === pathName) {
        callback()
    }
}