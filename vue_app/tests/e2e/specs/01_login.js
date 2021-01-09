// https://docs.cypress.io/api/introduction/api.html
import "cypress-localstorage-commands"

describe("Login", () => {
    before(() => {
        cy.login()
    })

    beforeEach(() => {
       cy.restoreLocalStorage()
    })
})
