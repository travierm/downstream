import {
    getNextGuid,
    setGuidIndex,
    findIndexByGuid,
} from "../../src/services/YoutubePlayerManager"

const initialGuidIndex = ["allen", "bob", "chris", "derek"]
const nextGuidIndex = ["apple", "banana", "cherry", "durian"]

beforeAll(() => {
    setGuidIndex(initialGuidIndex)
})

describe("YoutubePlayerManager", () => {
    it("it can find index by guid", () => {
        expect(findIndexByGuid("allen")).toBe(0)
        expect(findIndexByGuid("bob")).toBe(1)
        expect(findIndexByGuid("chris")).toBe(2)
        expect(findIndexByGuid("derek")).toBe(3)
    })

    it("it can get next guid in index", () => {
        expect(getNextGuid("allen")).toBe("bob")
        expect(getNextGuid("bob")).toBe("chris")
        expect(getNextGuid("chris")).toBe("derek")
        expect(getNextGuid("derek")).toBe("allen")
    })

    it("it can update guid index", () => {
        setGuidIndex(nextGuidIndex)

        expect(getNextGuid("apple")).toBe("banana")
        expect(getNextGuid("banana")).toBe("cherry")
        expect(getNextGuid("cherry")).toBe("durian")
        expect(getNextGuid("durian")).toBe("apple")
    })
})
