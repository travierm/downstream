import _ from "lodash";

export function numberWithCommas(x) {
	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

export function arrayNextIndex(array, currentIndex, direction = "+", steps = 1) {
    if(direction !== "+" && direction !== "-") {
        throw new Error("Direction param must be + for forward or - for backward. Neither is being used");
    }

    const arrayLength = array.length;
	const indexIValue = array.indexOf(currentIndex);
	if(indexIValue == -1) {
		throw new Error("Index given is not present in array.");
	}

	let nextIndex = false;

    if(direction == "+") {
      nextIndex = indexIValue + steps;
    }else{
      nextIndex = indexIValue - steps;
    }
    
    
    if(nextIndex >= arrayLength) {
      var remainder = nextIndex - arrayLength;
      return array[remainder];
    }
    
    
    if(nextIndex < 0) {
     var remainder = nextIndex + arrayLength;
     return array[remainder]
    }

	return array[nextIndex];
}