/**
 * Data Helper Functions
 */

export const dataHelpers = {
  /**
   * Generate random string
   */
  randomString(length = 10) {
    return Math.random()
      .toString(36)
      .substring(2, length + 2)
  },

  /**
   * Generate random email
   */
  randomEmail(prefix = 'test') {
    return `${prefix}_${this.randomString(8)}@maxcamrh.com`
  },

  /**
   * Generate random CPF (Brazilian tax ID)
   */
  randomCPF() {
    const n = () => Math.floor(Math.random() * 9)
    return `${n()}${n()}${n()}.${n()}${n()}${n()}.${n()}${n()}${n()}-${n()}${n()}`
  },

  /**
   * Generate random CNPJ (Brazilian company tax ID)
   */
  randomCNPJ() {
    const n = () => Math.floor(Math.random() * 9)
    return `${n()}${n()}.${n()}${n()}${n()}.${n()}${n()}${n()}/${n()}${n()}${n()}${n()}-${n()}${n()}`
  },

  /**
   * Generate random phone
   */
  randomPhone() {
    const n = () => Math.floor(Math.random() * 9)
    return `(${n()}${n()}) ${n()}${n()}${n()}${n()}${n()}-${n()}${n()}${n()}${n()}`
  },

  /**
   * Generate random CEP (Brazilian postal code)
   */
  randomCEP() {
    const n = () => Math.floor(Math.random() * 9)
    return `${n()}${n()}${n()}${n()}${n()}-${n()}${n()}${n()}`
  },

  /**
   * Generate admin data
   */
  generateAdminData(overrides = {}) {
    return {
      name: `Admin ${this.randomString(5)}`,
      email: this.randomEmail('admin'),
      cpf: this.randomCPF(),
      phone: this.randomPhone(),
      password: 'password',
      password_confirmation: 'password',
      ...overrides,
    }
  },

  /**
   * Generate franchise data
   */
  generateFranchiseData(overrides = {}) {
    return {
      name: `Franchise ${this.randomString(5)}`,
      email: this.randomEmail('franchise'),
      company_name: `Company ${this.randomString(5)}`,
      cnpj: this.randomCNPJ(),
      phone: this.randomPhone(),
      password: 'password',
      password_confirmation: 'password',
      // Address fields
      street: `Rua ${this.randomString(8)}`,
      number: Math.floor(Math.random() * 9999).toString(),
      complement: `Apto ${Math.floor(Math.random() * 99)}`,
      neighborhood: `Bairro ${this.randomString(6)}`,
      city: 'São Paulo',
      state: 'SP',
      zip_code: this.randomCEP(),
      ...overrides,
    }
  },

  /**
   * Generate client data
   */
  generateClientData(franchiseId, overrides = {}) {
    return {
      name: `Client ${this.randomString(5)}`,
      email: this.randomEmail('client'),
      company_name: `Client Company ${this.randomString(5)}`,
      cnpj: this.randomCNPJ(),
      phone: this.randomPhone(),
      franchise_id: franchiseId,
      password: 'password',
      password_confirmation: 'password',
      // Address fields
      street: `Rua ${this.randomString(8)}`,
      number: Math.floor(Math.random() * 9999).toString(),
      complement: `Sala ${Math.floor(Math.random() * 99)}`,
      neighborhood: `Bairro ${this.randomString(6)}`,
      city: 'Rio de Janeiro',
      state: 'RJ',
      zip_code: this.randomCEP(),
      ...overrides,
    }
  },

  /**
   * Generate collaborator data
   */
  generateCollaboratorData(clientId, overrides = {}) {
    return {
      name: `Collaborator ${this.randomString(5)}`,
      email: this.randomEmail('collaborator'),
      cpf: this.randomCPF(),
      phone: this.randomPhone(),
      client_id: clientId,
      position: 'Developer',
      salary: '5000.00',
      employment_type: 'CLT',
      start_date: '2024-01-01',
      password: 'password',
      password_confirmation: 'password',
      // Address fields
      street: `Rua ${this.randomString(8)}`,
      number: Math.floor(Math.random() * 9999).toString(),
      neighborhood: `Bairro ${this.randomString(6)}`,
      city: 'Belo Horizonte',
      state: 'MG',
      zip_code: this.randomCEP(),
      ...overrides,
    }
  },

  /**
   * Wait for specific time
   */
  wait(ms = 1000) {
    cy.wait(ms)
  },

  /**
   * Format currency
   */
  formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL',
    }).format(value)
  },

  /**
   * Format date
   */
  formatDate(date) {
    return new Intl.DateTimeFormat('pt-BR').format(new Date(date))
  },

  /**
   * Get current timestamp
   */
  timestamp() {
    return Date.now()
  },
}

export default dataHelpers
