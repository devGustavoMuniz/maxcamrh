// ***********************************************************
// This file is for component testing support
// ***********************************************************

import './commands'
import '@testing-library/cypress/add-commands'
import 'cypress-real-events'

// Import mount command
import { mount } from 'cypress/vue'

// Declare custom mount command
Cypress.Commands.add('mount', mount)

// Component testing specific configurations
beforeEach(() => {
  // Setup antes de cada teste de componente
})
