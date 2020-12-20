
/**
 * Global Functions
 * 
 * Functions that should be available to all scripts
 */


// Generate a unique element ID that won't cause conflicts with querying the dom
export function generateElementId() {
    return "egen_" + Math.random().toString(36).substr(2, 9);
}
