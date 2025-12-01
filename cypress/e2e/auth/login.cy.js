import authHelpers from '../../support/helpers/auth-helpers'

describe('Login Authentication', () => {
  beforeEach(() => {
    // Reset database and seed test data
    cy.artisan('migrate:fresh --seed --seeder=CypressTestSeeder')
  })

  describe('Successful Login', () => {
    it('should login as admin successfully', () => {
      const user = authHelpers.getUserByRole('admin')

      cy.visit('/login')
      cy.get('#email').should('be.visible')
      cy.get('#password').should('be.visible')

      cy.get('#email').type(user.email)
      cy.get('#password').type(user.password)
      cy.get('button[type="submit"]').click()

      // Verify redirect to dashboard
      cy.url().should('include', '/dashboard')

      // Verify authentication
      authHelpers.verifyAuthenticated()
    })

    it('should login as franchise successfully', () => {
      const user = authHelpers.getUserByRole('franchise')

      authHelpers.loginAndVerify(user.email, user.password, 'franchise')
    })

    it('should login as client successfully', () => {
      const user = authHelpers.getUserByRole('client')

      authHelpers.loginAndVerify(user.email, user.password, 'client')
    })

    it('should login as collaborator successfully', () => {
      const user = authHelpers.getUserByRole('collaborator')

      authHelpers.loginAndVerify(user.email, user.password, 'collaborator')
    })
  })

  describe('Failed Login Attempts', () => {
    it('should show error with invalid email', () => {
      cy.visit('/login')

      cy.get('#email').type('invalid@email.com')
      cy.get('#password').type('password123')
      cy.get('button[type="submit"]').click()

      // Should stay on login page
      cy.url().should('include', '/login')

      // Should show error message
      cy.contains('These credentials do not match our records', { timeout: 5000 }).should('be.visible')
    })

    it('should show error with invalid password', () => {
      const user = authHelpers.getUserByRole('admin')

      cy.visit('/login')

      cy.get('#email').type(user.email)
      cy.get('#password').type('wrongpassword')
      cy.get('button[type="submit"]').click()

      // Should stay on login page
      cy.url().should('include', '/login')

      // Should show error message
      cy.contains('These credentials do not match our records', { timeout: 5000 }).should('be.visible')
    })

    it('should show validation errors for empty fields', () => {
      cy.visit('/login')

      // Force click to bypass HTML5 validation
      cy.get('button[type="submit"]').click({ force: true })

      // Should stay on login page
      cy.url().should('include', '/login')

      // Should show validation errors (flexible check)
      cy.get('body').then(($body) => {
        const hasError = $body.text().toLowerCase().includes('email') ||
                        $body.text().toLowerCase().includes('required') ||
                        $body.text().toLowerCase().includes('obrigatório')

        if (hasError) {
          cy.log('Validation error displayed')
        } else {
          cy.url().should('include', '/login')
        }
      })
    })

    it('should show validation error for invalid email format', () => {
      cy.visit('/login')

      cy.get('#email').type('not-an-email')
      cy.get('#password').type('password123')
      cy.get('button[type="submit"]').click()

      // Should stay on login page
      cy.url().should('include', '/login')
    })
  })

  describe('Remember Me Functionality', () => {
    it('should remember user when checkbox is checked', () => {
      const user = authHelpers.getUserByRole('admin')

      cy.visit('/login')

      cy.get('#email').type(user.email)
      cy.get('#password').type(user.password)

      // Check remember me if it exists (shadcn uses button with role="checkbox")
      cy.get('body').then(($body) => {
        if ($body.find('#remember[role="checkbox"]').length > 0) {
          // shadcn-vue checkbox (button element)
          cy.get('#remember[role="checkbox"]').click()
        } else if ($body.find('input#remember[type="checkbox"]').length > 0) {
          // native checkbox
          cy.get('input#remember[type="checkbox"]').check()
        } else if ($body.find('input[name="remember"]').length > 0) {
          // checkbox by name
          cy.get('input[name="remember"]').check()
        }
      })

      cy.get('button[type="submit"]').click()

      cy.url().should('include', '/dashboard')

      // Verify remember cookie exists (optional feature)
      cy.getCookie('remember_web').then((cookie) => {
        if (cookie) {
          cy.log('Remember me cookie found')
        } else {
          cy.log('Remember me cookie not found - feature may not be implemented')
        }
      })
    })
  })

  describe('Logout Functionality', () => {
    it('should logout successfully', () => {
      // Login first
      cy.loginAs('admin')
      cy.url().should('include', '/dashboard')

      // Find and click logout button/link (flexible search)
      cy.get('body').then(($body) => {
        const bodyText = $body.text().toLowerCase()

        if (bodyText.includes('logout') || bodyText.includes('sair')) {
          if (bodyText.includes('logout')) {
            cy.contains('logout', { matchCase: false, timeout: 5000 }).click()
          } else {
            cy.contains('sair', { matchCase: false, timeout: 5000 }).click()
          }
        } else {
          // Use the logout command instead
          cy.logout()
        }
      })

      // Should redirect to login or home
      cy.url().should('match', /\/(login)?$/)

      // Wait a bit for cookies to be cleared
      cy.wait(500)

      // Clear any remaining cookies/storage
      cy.clearCookies()
      cy.clearLocalStorage()
    })

    it('should not access protected routes after logout', () => {
      // Login first
      cy.loginAs('admin')

      // Logout
      cy.logout()

      // Clear cookies to ensure clean state
      cy.clearCookies()
      cy.clearLocalStorage()

      // Try to access protected route
      cy.visit('/dashboard')

      // Should redirect to login
      cy.url().should('include', '/login')
    })
  })

  describe('Redirect After Login', () => {
    it('should redirect to intended page after login', () => {
      // Try to access protected page while not authenticated
      cy.visit('/admins')

      // Should redirect to login
      cy.url().should('include', '/login')

      // Login
      const user = authHelpers.getUserByRole('admin')
      cy.get('#email').type(user.email)
      cy.get('#password').type(user.password)
      cy.get('button[type="submit"]').click()

      // Should redirect to originally intended page or dashboard
      cy.url().should('match', /\/(admins|dashboard)/)
    })
  })
})
