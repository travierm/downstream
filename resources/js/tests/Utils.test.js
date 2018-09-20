import { arrayNextIndex }  from '../services/Utils';

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
	})
})