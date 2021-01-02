// https://docs.cypress.io/api/introduction/api.html
import "cypress-localstorage-commands"

describe("Login", () => {
    before(() => {
        cy.login()
    })

    beforeEach(() => {
       cy.restoreLocalStorage()
    })

    it('Can see landing page', () => {
        cy.visit('/')
        cy.contains('Welcome to downstream')
    })

    it("Can see collection", () => {                 
        cy.visit("/collection")
        cy.contains("div", "Kid Cudi - Mr. Solo Dolo III")
    })

    it("/login redirects to /collection when loggedIn", () => {
        cy.visit("/login")
        cy.contains("div", "Kid Cudi - Mr. Solo Dolo III")
    })
})
