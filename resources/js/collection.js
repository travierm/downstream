import { onRouteLoad } from "./helpers";

onRouteLoad('/collection', () => {
  let observer;
  let options = {
    root: null, // observing relative to the viewport
    rootMargin: "20px", // Margin around the root. Values are similar to css property. Unitless values not allowed
    threshold: 0.1, // So, 10% of the item's visibility
  };

  observer = new IntersectionObserver(handleIntersect, options);
  observer.observe(document.querySelector("#loader"));

  function handleIntersect(entries, observer) {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        // Logic to load more images
        //console.log("Loader is visible, load more images!");
        getCollectionSlize();
        // After images have loaded, you might want to observe the new loader position if it has moved.
        // observer.unobserve(entry.target); // Optionally unobserve the current target
        // observer.observe(document.querySelector("#newLoader")); // If your loader moves or you need to observe a new target
      }
    });
  }
})

const offset = 8;
let sliceStart = 17;

function getCollectionSlize() {

  console.log('fetching', sliceStart)
  window.axios.get(`/collection/slice?start=${sliceStart}&limit=${offset}`)
    .then((res) => {
      const html = res.data;

      if (html === "") {
        console.info("No more items to load!");
        return;
      }

      var tempDiv = document.createElement("div");
      tempDiv.innerHTML = html;

      while (tempDiv.firstChild) {
        document
          .querySelector("#collection")
          ?.appendChild(tempDiv.firstChild);
      }
    })
    .finally(() => {
      sliceStart += offset;
    });
}
