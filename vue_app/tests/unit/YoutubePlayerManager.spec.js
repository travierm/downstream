import {
    setGuidIndex,
    getNextCard,
    getNextGuid,
    findIndexByGuid,
} from "../../src/services/YoutubePlayerManager"

const initialGuidIndex = ["allen", "bob", "chris", "derek"]

beforeAll(() => {
    setGuidIndex(initialGuidIndex)
})

describe("Basic", () => {
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
})
