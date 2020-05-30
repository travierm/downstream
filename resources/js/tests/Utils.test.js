import { arrayNextIndex }  from '../services/Utils.js';

describe('Global Functions', () => {
	it('arrayNextIndex', () => {
        const array = [
            "A",
            "B",
            "C",
            "D"
        ];

        expect(arrayNextIndex(array, "A", "+")).toBe("B");
        expect(arrayNextIndex(array, "B", "-")).toBe("A");
        expect(arrayNextIndex(array, "D", "+")).toBe("A");
        expect(arrayNextIndex(array, "A", "-")).toBe("D");

        //test stepping
        expect(arrayNextIndex(array, "A", "-", 2)).toBe("C");
        expect(arrayNextIndex(array, "B", "+", 3)).toBe("A");

        //test error handling for bad index /force travis ci
        expect(() => {
            arrayNextIndex(array, "J", "+")
        }).toThrow();

        //test big array
        let bigArray = [];
        for(var i = 1; i <= 500; i++) {
            bigArray.push(i);
        }

        expect(arrayNextIndex(bigArray, 99, "+")).toBe(100);
        expect(arrayNextIndex(bigArray, 300, "+")).toBe(301);
        expect(arrayNextIndex(bigArray, 55, "+", 5)).toBe(60);
        expect(arrayNextIndex(bigArray, 10, "-", 15)).toBe(495);
	})
})