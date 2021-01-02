import YoutubePlayerManager from "../../src/services/YoutubePlayerManager"

const initialGuidIndex = ["allen", "bob", "chris", "derek"]
const nextGuidIndex = ["apple", "banana", "cherry", "durian"]

beforeAll(() => {
    YoutubePlayerManager.setGuidIndex(initialGuidIndex)
})

describe("YoutubePlayerManager", () => {
    it("it can find index by guid", () => {
        expect(YoutubePlayerManager.findIndexByGuid("allen")).toBe(0)
        expect(YoutubePlayerManager.findIndexByGuid("bob")).toBe(1)
        expect(YoutubePlayerManager.findIndexByGuid("chris")).toBe(2)
        expect(YoutubePlayerManager.findIndexByGuid("derek")).toBe(3)
    })

    it("it can get next guid in index", () => {
        expect(YoutubePlayerManager.getNextGuid("allen")).toBe("bob")
        expect(YoutubePlayerManager.getNextGuid("bob")).toBe("chris")
        expect(YoutubePlayerManager.getNextGuid("chris")).toBe("derek")
        expect(YoutubePlayerManager.getNextGuid("derek")).toBe("allen")
    })

    it("it can update guid index", () => {
        YoutubePlayerManager.setGuidIndex(nextGuidIndex)

        expect(YoutubePlayerManager.getNextGuid("apple")).toBe("banana")
        expect(YoutubePlayerManager.getNextGuid("banana")).toBe("cherry")
        expect(YoutubePlayerManager.getNextGuid("cherry")).toBe("durian")
        expect(YoutubePlayerManager.getNextGuid("durian")).toBe("apple")
    })
})
