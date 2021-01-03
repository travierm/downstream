// https://docs.cypress.io/api/introduction/api.html
import "cypress-localstorage-commands"

describe("Collection", () => {
    before(() => {
        cy.login()
    })

    beforeEach(() => {
        cy.restoreLocalStorage()
    })

    it("Can search for and collect item", () => {
        // Search for drake
        cy.search("drake")

        // Collect first search result item
        cy.get(".collectBtn:first").click()

        cy.wait(1000)

        // Make sure item exists in user collection
        cy.visit("/collection")
        cy.contains("Drake")
    })

    it("Can play video", () => {
        // Play First Video
        cy.get(".youtubeCardThumbnail:first").click()

        cy.wait(1000)

        cy.get("iframe").should("be.visible")
    })

    it("Video does not show thumbnail when playing", () => {

        // Ensure only one card is showing its thumbnail
        cy.get("div")
            .find(".youtubeCardTitle")
            .should("not.be.visible")
    })


    it("Can remove item from collection", () => {
        // Collect first search result item
        cy.get(".removeBtn:first").click()
        cy.get("#confirmDialogYesBtn").click()

        // Make sure item exists in user collection
        cy.contains("Drake").should("not.exist")
    })
})
