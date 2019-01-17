import VueScrollTo from 'vue-scrollto';

export function numberWithCommas(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

export function generateElementId() {
  return Math.random().toString(36).substr(2, 9);
}

export function arrayNextIndex(array, currentIndex, direction = '+', steps = 1) {
  let remainder = false;

  if (direction !== '+' && direction !== '-') {
    throw new Error('Direction param must be + for forward or - for backward. Neither is being used');
  }

  const arrayLength = array.length;
  const indexIValue = array.indexOf(currentIndex);
  if (indexIValue === -1) {
    throw new Error('Index given is not present in array.');
  }

  let nextIndex = false;

  if (direction === '+') {
    nextIndex = indexIValue + steps;
  } else {
    nextIndex = indexIValue - steps;
  }


  if (nextIndex >= arrayLength) {
    remainder = nextIndex - arrayLength;
    return array[remainder];
  }


  if (nextIndex < 0) {
    remainder = nextIndex + arrayLength;
    return array[remainder];
  }

  return array[nextIndex];
}

export function focusOnElement(elementId) {
  const query = `[id='${elementId}']`;

  return VueScrollTo.scrollTo(query);
}
