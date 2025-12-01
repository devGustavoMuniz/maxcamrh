describe('Login Debug Test', () => {
  it('should debug login process step by step', () => {
    cy.visit('/login')

    // Check if page loaded
    cy.log('✅ Page loaded')

    // Check email input
    cy.get('#email').then(($input) => {
      cy.log('Email input found:', $input)
      cy.log('Is visible:', $input.is(':visible'))
      cy.log('Is disabled:', $input.is(':disabled'))
      cy.log('Type:', $input.attr('type'))
    })

    // Check password input
    cy.get('#password').then(($input) => {
      cy.log('Password input found:', $input)
      cy.log('Is visible:', $input.is(':visible'))
      cy.log('Is disabled:', $input.is(':disabled'))
      cy.log('Type:', $input.attr('type'))
    })

    // Try typing email
    cy.log('Typing email...')
    cy.get('#email').should('be.visible').clear()
    cy.wait(500)
    cy.get('#email').type('admin@maxcamrh.com', { delay: 100 })
    cy.wait(500)

    // Check if email was typed
    cy.get('#email').should('have.value', 'admin@maxcamrh.com')
    cy.log('✅ Email typed successfully')

    // Try typing password
    cy.log('Typing password...')
    cy.get('#password').should('be.visible').clear()
    cy.wait(500)
    cy.get('#password').type('password', { delay: 100 })
    cy.wait(500)

    // Check if password was typed
    cy.get('#password').should('have.value', 'password')
    cy.log('✅ Password typed successfully')

    // Check submit button
    cy.get('button[type="submit"]').then(($btn) => {
      cy.log('Submit button found:', $btn)
      cy.log('Is visible:', $btn.is(':visible'))
      cy.log('Is disabled:', $btn.is(':disabled'))
      cy.log('Text:', $btn.text())
    })

    // Try submitting
    cy.log('Clicking submit...')
    cy.get('button[type="submit"]').click()

    // Wait for response
    cy.wait(2000)

    // Check URL
    cy.url().then((url) => {
      cy.log('Current URL:', url)
    })
  })
})
