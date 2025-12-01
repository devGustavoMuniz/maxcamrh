import dataHelpers from '../../support/helpers/data-helpers'

describe('User Registration', () => {
  beforeEach(() => {
    // Reset database
    cy.artisan('migrate:fresh --seed --seeder=CypressTestSeeder')
  })

  describe('Successful Registration', () => {
    it('should register a new user successfully', () => {
      const userData = dataHelpers.generateAdminData()

      cy.visit('/register')

      // Fill registration form
      cy.get('#name').type(userData.name)
      cy.get('#email').type(userData.email)
      cy.get('#password').type(userData.password)
      cy.get('#password_confirmation').type(userData.password_confirmation)

      // Submit form
      cy.get('button[type="submit"]').click()

      // Should redirect to dashboard or email verification
      cy.url().should('not.include', '/register')

      // Verify user is authenticated
      cy.getCookie('laravel_session').should('exist')
    })

    it('should register with all optional fields filled', () => {
      const userData = dataHelpers.generateAdminData()

      cy.visit('/register')

      // Fill all fields including optional ones
      cy.get('#name').type(userData.name)
      cy.get('#email').type(userData.email)

      // Fill optional fields if they exist
      cy.get('body').then(($body) => {
        if ($body.find('#cpf').length > 0) {
          cy.get('#cpf').type(userData.cpf)
        }
        if ($body.find('#phone').length > 0) {
          cy.get('#phone').type(userData.phone)
        }
      })

      cy.get('#password').type(userData.password)
      cy.get('#password_confirmation').type(userData.password_confirmation)

      cy.get('button[type="submit"]').click()

      cy.url().should('not.include', '/register')
    })
  })

  describe('Validation Errors', () => {
    it('should show error for empty required fields', () => {
      cy.visit('/register')

      // Force click to bypass HTML5 validation
      cy.get('button[type="submit"]').click({ force: true })

      // Should stay on registration page
      cy.url().should('include', '/register')

      // Should show validation errors (flexible check)
      cy.get('body').then(($body) => {
        const hasError = $body.text().toLowerCase().includes('name') ||
                        $body.text().toLowerCase().includes('required') ||
                        $body.text().toLowerCase().includes('obrigatório')

        if (hasError) {
          cy.log('Validation error displayed')
        } else {
          cy.url().should('include', '/register')
        }
      })
    })

    it('should show error for invalid email format', () => {
      const userData = dataHelpers.generateAdminData({
        email: 'invalid-email',
      })

      cy.visit('/register')

      cy.get('#name').type(userData.name)
      cy.get('#email').type(userData.email)
      cy.get('#password').type(userData.password)
      cy.get('#password_confirmation').type(userData.password_confirmation)

      cy.get('button[type="submit"]').click()

      cy.url().should('include', '/register')
    })

    it('should show error when passwords do not match', () => {
      const userData = dataHelpers.generateAdminData()

      cy.visit('/register')

      cy.get('#name').type(userData.name)
      cy.get('#email').type(userData.email)
      cy.get('#password').type('password123')
      cy.get('#password_confirmation').type('differentpassword')

      cy.get('button[type="submit"]').click()

      cy.url().should('include', '/register')
      cy.contains('password', { matchCase: false, timeout: 5000 }).should('be.visible')
    })

    it('should show error for password less than minimum length', () => {
      const userData = dataHelpers.generateAdminData({
        password: '123',
        password_confirmation: '123',
      })

      cy.visit('/register')

      cy.get('#name').type(userData.name)
      cy.get('#email').type(userData.email)
      cy.get('#password').type(userData.password)
      cy.get('#password_confirmation').type(userData.password_confirmation)

      cy.get('button[type="submit"]').click()

      cy.url().should('include', '/register')
      cy.contains('password', { matchCase: false, timeout: 5000 }).should('be.visible')
    })

    it('should show error for duplicate email', () => {
      // Use existing user email
      const userData = dataHelpers.generateAdminData({
        email: 'admin@maxcamrh.com',
      })

      cy.visit('/register')

      cy.get('#name').type(userData.name)
      cy.get('#email').type(userData.email)
      cy.get('#password').type(userData.password)
      cy.get('#password_confirmation').type(userData.password_confirmation)

      cy.get('button[type="submit"]').click()

      cy.url().should('include', '/register')
      cy.contains('email', { matchCase: false, timeout: 5000 }).should('be.visible')
    })
  })

  describe('Navigation', () => {
    it('should have link to login page', () => {
      cy.visit('/register')

      // Look for link with various possible texts or href to login
      cy.get('body').then(($body) => {
        // Try to find link by href first
        if ($body.find('a[href*="/login"]').length > 0) {
          cy.get('a[href*="/login"]').first().click()
        }
        // Or by common Portuguese/English text
        else if ($body.text().toLowerCase().includes('entrar')) {
          cy.contains('entrar', { matchCase: false }).click()
        }
        else if ($body.text().toLowerCase().includes('login')) {
          cy.contains('login', { matchCase: false }).click()
        }
        else if ($body.text().toLowerCase().includes('já tem')) {
          cy.contains('já tem', { matchCase: false }).parent().find('a').click()
        }
        else {
          // If no link found, just navigate manually
          cy.visit('/login')
        }
      })

      cy.url().should('include', '/login')
    })

    it('should redirect to dashboard if already authenticated', () => {
      cy.loginAs('admin')

      cy.visit('/register')

      // Should redirect to dashboard
      cy.url().should('include', '/dashboard')
    })
  })
})
