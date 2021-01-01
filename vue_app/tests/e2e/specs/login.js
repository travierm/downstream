// https://docs.cypress.io/api/introduction/api.html
import "cypress-localstorage-commands"

describe("Login", () => {
    before(() => {
        cy.login()
    })

    beforeEach(() => {
       cy.restoreLocalStorage()
    })

    it("Can see collection", () => {                                 
        cy.contains("div", "Kid Cudi - Mr. Solo Dolo III")
    })
})
