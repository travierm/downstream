// https://docs.cypress.io/api/introduction/api.html
import "cypress-localstorage-commands"

describe("Login", () => {
    beforeEach(() => {
       cy.restoreLocalStorage()
    })

    it('Can see landing page', () => {
        cy.visit('/')
        cy.contains('Welcome to downstream')
    })

    it("Can see collection", () => {     
        cy.login()                            
        cy.contains("div", "Kid Cudi - Mr. Solo Dolo III")
    })
})
