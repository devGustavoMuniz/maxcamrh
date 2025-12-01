// ***********************************************
// This file contains custom Cypress commands for MaxCam RH
// ***********************************************

/**
 * Login command - Faz login com credenciais
 * @example cy.login('admin@example.com', 'password')
 */
Cypress.Commands.add('login', (email, password) => {
  cy.visit('/login')

  // Wait for page to be fully loaded
  cy.waitForPageLoad()

  // Wait for email input to be visible and enabled
  cy.get('#email').should('be.visible').should('not.be.disabled')
  cy.get('#email').clear().type(email, { delay: 50 })

  // Wait for password input to be visible and enabled
  cy.get('#password').should('be.visible').should('not.be.disabled')
  cy.get('#password').clear().type(password, { delay: 50 })

  // Click submit
  cy.get('button[type="submit"]').should('be.visible').should('not.be.disabled').click()

  // Verify redirect
  cy.url().should('not.include', '/login')
})

/**
 * Login as specific role - Faz login com usuário de uma role específica
 * @example cy.loginAs('admin')
 */
Cypress.Commands.add('loginAs', (role) => {
  const credentials = {
    admin: {
      email: 'admin@maxcamrh.com',
      password: 'password',
    },
    franchise: {
      email: 'franchise@maxcamrh.com',
      password: 'password',
    },
    client: {
      email: 'client@maxcamrh.com',
      password: 'password',
    },
    collaborator: {
      email: 'collaborator@maxcamrh.com',
      password: 'password',
    },
  }

  const user = credentials[role]
  if (!user) {
    throw new Error(`Role ${role} not found. Available: admin, franchise, client, collaborator`)
  }

  cy.login(user.email, user.password)
})

/**
 * Logout command
 */
Cypress.Commands.add('logout', () => {
  // Get CSRF token from cookie before logout
  cy.getCookie('XSRF-TOKEN').then((cookie) => {
    const token = cookie ? decodeURIComponent(cookie.value) : null

    cy.request({
      method: 'POST',
      url: '/logout',
      failOnStatusCode: false,
      headers: token ? {
        'X-XSRF-TOKEN': token
      } : {}
    })
  })

  cy.clearCookies()
  cy.clearLocalStorage()
})

/**
 * Reset database via artisan command
 */
Cypress.Commands.add('resetDatabase', () => {
  cy.exec('./vendor/bin/sail artisan migrate:fresh --seed --env=testing', {
    timeout: 60000,
    failOnNonZeroExit: false,
  })
})

/**
 * Seed database with specific seeder
 */
Cypress.Commands.add('seedDatabase', (seeder = 'DatabaseSeeder') => {
  cy.exec(`./vendor/bin/sail artisan db:seed --class=${seeder} --env=testing`, {
    timeout: 30000,
    failOnNonZeroExit: false,
  })
})

/**
 * Run artisan command
 */
Cypress.Commands.add('artisan', (command) => {
  cy.exec(`./vendor/bin/sail artisan ${command}`, {
    timeout: 30000,
    failOnNonZeroExit: false,
  })
})

/**
 * Visit Inertia route - Navega para uma rota Inertia
 */
Cypress.Commands.add('visitInertia', (route, options = {}) => {
  cy.visit(route, {
    ...options,
    onBeforeLoad: (win) => {
      // Garante que Inertia está disponível
      win.Inertia = win.Inertia || {}
    },
  })
  cy.waitForInertia()
})

/**
 * Fill form dynamically - Preenche formulário com objeto de dados
 * @example cy.fillForm({ name: 'John', email: 'john@example.com' })
 */
Cypress.Commands.add('fillForm', (formData) => {
  Object.keys(formData).forEach((field) => {
    const value = formData[field]

    // Try multiple selectors: id first, then name
    const selectors = [
      `#${field}`,
      `input[name="${field}"]`,
      `select[name="${field}"]`,
      `textarea[name="${field}"]`
    ]

    let found = false

    selectors.forEach((selector) => {
      if (!found) {
        cy.get('body').then(($body) => {
          if ($body.find(selector).length > 0) {
            found = true

            cy.get(selector).should('be.visible').then(($el) => {
              const tagName = $el.prop('tagName').toLowerCase()
              const type = $el.attr('type')

              if (tagName === 'select') {
                cy.get(selector).select(value)
              } else if (type === 'checkbox') {
                if (value) {
                  cy.get(selector).check()
                } else {
                  cy.get(selector).uncheck()
                }
              } else if (type === 'radio') {
                cy.get(`${selector}[value="${value}"]`).check()
              } else {
                cy.get(selector).should('not.be.disabled').clear().type(value.toString(), { delay: 50 })
              }
            })
          }
        })
      }
    })
  })
})

/**
 * Wait for alert/toast message
 */
Cypress.Commands.add('waitForToast', (message, type = 'success') => {
  cy.contains(message, { timeout: 10000 }).should('be.visible')
})

/**
 * Check if authenticated
 */
Cypress.Commands.add('isAuthenticated', () => {
  cy.getCookie('laravel_session').should('exist')
})

/**
 * Check if not authenticated
 */
Cypress.Commands.add('isNotAuthenticated', () => {
  cy.getCookie('laravel_session').should('not.exist')
})

/**
 * Create resource via API
 */
Cypress.Commands.add('createResource', (endpoint, data) => {
  return cy.request({
    method: 'POST',
    url: endpoint,
    body: data,
    failOnStatusCode: false,
  })
})

/**
 * Delete resource via API
 */
Cypress.Commands.add('deleteResource', (endpoint) => {
  return cy.request({
    method: 'DELETE',
    url: endpoint,
    failOnStatusCode: false,
  })
})

/**
 * Intercept Inertia requests
 */
Cypress.Commands.add('interceptInertia', (method = 'GET', url = '**') => {
  cy.intercept({
    method,
    url,
    headers: {
      'X-Inertia': 'true',
    },
  }).as('inertiaRequest')
})

/**
 * Wait for Inertia navigation
 */
Cypress.Commands.add('waitForInertiaNavigation', () => {
  cy.wait('@inertiaRequest', { timeout: 10000 })
})

/**
 * Check validation errors
 */
Cypress.Commands.add('shouldHaveValidationError', (field, message) => {
  cy.get(`[data-error="${field}"]`)
    .should('be.visible')
    .and('contain', message)
})

/**
 * Upload file
 */
Cypress.Commands.add('uploadFile', (selector, fileName, fileType = 'image/png') => {
  cy.get(selector).selectFile({
    contents: `cypress/fixtures/${fileName}`,
    mimeType: fileType,
  })
})
