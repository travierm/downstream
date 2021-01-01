// https://docs.cypress.io/api/introduction/api.html

describe("Basic App Features", () => {
    beforeEach(() => {
        cy.login()
    })

    /*afterEach(() => {
        cy.logout()
    })*/

    it("Can see collection", () => {
        cy.contains("div", "Kid Cudi - Mr. Solo Dolo III")
    })

    it("Can search for and collect item", () => {
        // Search for drake
        cy.search('drake')

        // Collect first search result item
        cy.get(".collectBtn:first").click()

        // Make sure item exists in user collection
        cy.visit("/collection")
        cy.contains('Drake')
    })

    it("Can remove item from collection", () => {
        // Collect first search result item
        cy.get(".removeBtn:first").click()
        cy.get('#confirmDialogYesBtn').click()

        // Make sure item exists in user collection
        cy.contains("Drake").should("not.exist")
    })
})
