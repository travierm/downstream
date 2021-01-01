// https://docs.cypress.io/api/introduction/api.html

describe('Basic App Features', () => {
  it('Visits the app root url', () => {
    cy.visit('/')
    cy.contains('div', 'downstream')
  })

  it("Can login and see collection", () => {
      cy.visit("/login")
      cy.get("input[name=email]").type('test@gmail.com')
      cy.get("input[name=password]").type(`password{enter}`)

      cy.contains("div", "Kid Cudi - Mr. Solo Dolo III")
  })
})
