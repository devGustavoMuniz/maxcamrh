import { defineConfig } from 'cypress'

export default defineConfig({
  e2e: {
    baseUrl: 'http://localhost',
    specPattern: 'cypress/e2e/**/*.cy.{js,jsx,ts,tsx}',
    supportFile: 'cypress/support/e2e.js',
    fixturesFolder: 'cypress/fixtures',
    screenshotsFolder: 'cypress/screenshots',
    videosFolder: 'cypress/videos',
    downloadsFolder: 'cypress/downloads',

    // Viewport padrão
    viewportWidth: 1280,
    viewportHeight: 720,

    // Configurações de timeout
    defaultCommandTimeout: 10000,
    requestTimeout: 10000,
    responseTimeout: 30000,
    pageLoadTimeout: 30000,

    // Configurações de retry
    retries: {
      runMode: 2,
      openMode: 0,
    },

    // Vídeo apenas em modo CI
    video: true,
    videoCompression: 15,

    // Screenshots apenas em falhas
    screenshotOnRunFailure: true,

    // Variáveis de ambiente
    env: {
      apiUrl: 'http://localhost',
      coverage: false,
    },

    setupNodeEvents(on, config) {
      // Implementar event listeners aqui se necessário

      // Logs mais detalhados
      on('task', {
        log(message) {
          console.log(message)
          return null
        },
        table(message) {
          console.table(message)
          return null
        },
      })

      return config
    },

    // Excluir arquivos de HMR do Vite
    excludeSpecPattern: [
      '**/node_modules/**',
      '**/dist/**',
      '**/build/**',
      '**/.vite/**',
    ],
  },

  component: {
    devServer: {
      framework: 'vue',
      bundler: 'vite',
    },
    specPattern: 'resources/js/**/*.cy.{js,jsx,ts,tsx}',
    supportFile: 'cypress/support/component.js',
  },
})
