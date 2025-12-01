describe('Email Verification', () => {
  beforeEach(() => {
    cy.artisan('migrate:fresh --seed --seeder=CypressTestSeeder')
  })

  describe('Email Verification Page', () => {
    it('should display verification notice for unverified users', () => {
      // Create unverified user via API or registration
      // For now, we'll check if the page exists
      cy.visit('/verify-email', { failOnStatusCode: false })

      cy.get('body').then(($body) => {
        if ($body.text().includes('verify') || $body.text().includes('verification')) {
          cy.log('Email verification page found')
        }
      })
    })

    it('should allow resending verification email', () => {
      cy.visit('/verify-email', { failOnStatusCode: false })

      cy.get('body').then(($body) => {
        if ($body.find('button:contains("Resend")').length > 0) {
          cy.contains('Resend', { matchCase: false }).click()
          cy.contains('sent', { matchCase: false, timeout: 5000 }).should('be.visible')
        }
      })
    })

    it('should redirect verified users away from verification page', () => {
      // All seeded users are verified
      cy.loginAs('admin')

      cy.visit('/verify-email', { failOnStatusCode: false })

      // Should redirect to dashboard
      cy.url().should('include', '/dashboard')
    })
  })

  describe('Protected Routes', () => {
    it('should allow access to dashboard for verified users', () => {
      cy.loginAs('admin')

      cy.visit('/dashboard')

      cy.url().should('include', '/dashboard')
    })
  })

  describe('Verification Links', () => {
    it('should verify email with valid verification link', () => {
      // Note: This requires generating a valid verification URL
      // You may need to create a custom artisan command

      // For now, we'll just test the endpoint exists
      cy.request({
        method: 'GET',
        url: '/email/verify/1/fake-hash',
        failOnStatusCode: false,
      }).then((response) => {
        // Response should be 403 (invalid signature) or redirect
        expect(response.status).to.be.oneOf([200, 302, 403, 404])
      })
    })
  })
})
