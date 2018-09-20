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
	})
})