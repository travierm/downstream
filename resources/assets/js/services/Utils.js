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
	let nextIndex = false;

	if(direction == "+") {
		nextIndex = indexIValue + steps;
	}else{
		nextIndex = indexIValue - steps;
	}

	if(nextIndex > arrayLength) {
		
	}

	return array[nextIndex];
}