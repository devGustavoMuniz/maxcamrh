// ***********************************************************
// This file is processed and loaded automatically before your test files.
//
// You can change the location of this file or turn off automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

// Import commands.js using ES2015 syntax:
import './commands'

// Import Testing Library
import '@testing-library/cypress/add-commands'

// Import Cypress Real Events
import 'cypress-real-events'

// Configurações globais
Cypress.on('uncaught:exception', (err, runnable) => {
  // Previne que exceções não capturadas falhem os testes
  // Útil para erros do Inertia.js e Vue.js que não afetam funcionalidade
  if (err.message.includes('ResizeObserver loop')) {
    return false
  }
  if (err.message.includes('NotFoundError')) {
    return false
  }
  // Permite que o teste continue
  return true
})

// Before each test
beforeEach(() => {
  // Limpar cookies e local storage
  cy.clearCookies()
  cy.clearLocalStorage()

  // Aguardar Inertia carregar
  cy.intercept('**/inertia/**').as('inertiaRequest')
})

// After each test
afterEach(function () {
  // Capturar screenshot em caso de falha
  if (this.currentTest.state === 'failed') {
    cy.screenshot(`${this.currentTest.title} - Failed`)
  }
})

// Comandos globais úteis
Cypress.Commands.add('waitForPageLoad', () => {
  cy.window().its('document.readyState').should('equal', 'complete')
})

Cypress.Commands.add('waitForInertia', (timeout = 5000) => {
  cy.window({ timeout }).should('have.property', 'Inertia')
  cy.window({ timeout }).its('Inertia').should('exist')
})

Cypress.Commands.add('logMessage', (message, type = 'info') => {
  cy.task('log', `[${type.toUpperCase()}] ${message}`)
})
