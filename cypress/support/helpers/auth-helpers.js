/**
 * Authentication Helper Functions
 */

export const authHelpers = {
  /**
   * Get user credentials by role
   */
  getUserByRole(role) {
    const users = {
      admin: {
        name: 'Admin User',
        email: 'admin@maxcamrh.com',
        password: 'password',
        role: 'admin',
      },
      franchise: {
        name: 'Franchise User',
        email: 'franchise@maxcamrh.com',
        password: 'password',
        role: 'franchise',
      },
      client: {
        name: 'Client User',
        email: 'client@maxcamrh.com',
        password: 'password',
        role: 'client',
      },
      collaborator: {
        name: 'Collaborator User',
        email: 'collaborator@maxcamrh.com',
        password: 'password',
        role: 'collaborator',
      },
    }

    return users[role] || null
  },

  /**
   * Verify user is on correct page after login based on role
   */
  verifyRoleLanding(role) {
    switch (role) {
      case 'admin':
        cy.url().should('include', '/dashboard')
        break
      case 'franchise':
        cy.url().should('include', '/dashboard')
        break
      case 'client':
        cy.url().should('include', '/dashboard')
        break
      case 'collaborator':
        cy.url().should('include', '/dashboard')
        break
      default:
        cy.url().should('include', '/dashboard')
    }
  },

  /**
   * Verify authentication state
   */
  verifyAuthenticated() {
    cy.getCookie('laravel_session').should('exist')
    cy.getCookie('XSRF-TOKEN').should('exist')
  },

  /**
   * Verify not authenticated state
   */
  verifyNotAuthenticated() {
    cy.getCookie('laravel_session').should('not.exist')
  },

  /**
   * Login and verify success
   */
  loginAndVerify(email, password, expectedRole = null) {
    cy.visit('/login')

    // Wait for page to be fully loaded
    cy.waitForPageLoad()

    // Wait and fill email
    cy.get('#email').should('be.visible').should('not.be.disabled')
    cy.get('#email').clear().type(email, { delay: 50 })

    // Wait and fill password
    cy.get('#password').should('be.visible').should('not.be.disabled')
    cy.get('#password').clear().type(password, { delay: 50 })

    // Submit
    cy.get('button[type="submit"]').should('be.visible').should('not.be.disabled').click()

    // Verify redirect
    cy.url().should('not.include', '/login')

    // Verify cookies
    this.verifyAuthenticated()

    // Verify role landing if provided
    if (expectedRole) {
      this.verifyRoleLanding(expectedRole)
    }
  },

  /**
   * Register new user
   */
  register(userData) {
    cy.visit('/register')
    cy.get('#name').type(userData.name)
    cy.get('#email').type(userData.email)
    cy.get('#password').type(userData.password)
    cy.get('#password_confirmation').type(userData.password)
    cy.get('button[type="submit"]').click()
  },

  /**
   * Request password reset
   */
  requestPasswordReset(email) {
    cy.visit('/forgot-password')
    cy.get('#email').type(email)
    cy.get('button[type="submit"]').click()
  },

  /**
   * Logout and verify
   */
  logoutAndVerify() {
    cy.request({
      method: 'POST',
      url: '/logout',
      failOnStatusCode: false,
    })

    cy.clearCookies()
    cy.clearLocalStorage()

    this.verifyNotAuthenticated()
  },
}

export default authHelpers
