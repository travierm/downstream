export default class Utils {
	constructor() {

	}

	//10000 -> 10,000
	numberWithCommas(x) {
    	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
}