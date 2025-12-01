# Cypress E2E Tests - MaxCam RH

## 🚀 Como Executar os Testes

### Pré-requisitos

1. Certifique-se de que o ambiente Laravel Sail está rodando:
```bash
./vendor/bin/sail up -d
```

2. Certifique-se de que o frontend está compilado:
```bash
npm run dev
# ou para produção
npm run build
```

### Comandos Disponíveis

#### 1. Modo Interativo (Desenvolvimento)
Abre a interface gráfica do Cypress para executar testes individualmente:

```bash
npm run cypress:open
```

ou especificamente para E2E com Chrome:

```bash
npm run test:e2e:dev
```

#### 2. Modo Headless (CI/CD)
Executa todos os testes em modo headless (sem interface):

```bash
npm run cypress:run
```

ou especificamente com Chrome:

```bash
npm run cypress:run:chrome
```

#### 3. Modo Headed (Ver execução)
Executa os testes mas mostra o browser:

```bash
npm run cypress:run:headed
```

#### 4. Executar teste específico
```bash
npx cypress run --spec "cypress/e2e/auth/login.cy.js"
```

#### 5. Executar por pasta
```bash
npx cypress run --spec "cypress/e2e/auth/**/*.cy.js"
```

### Preparar Banco de Dados para Testes

Antes de executar os testes, rode o seeder específico:

```bash
./vendor/bin/sail artisan migrate:fresh --seed --seeder=CypressTestSeeder
```

Ou dentro dos testes, eles já fazem isso automaticamente via comando `cy.artisan()`.

## 📁 Estrutura de Testes

```
cypress/
├── e2e/
│   ├── auth/                    # Testes de autenticação
│   │   ├── login.cy.js
│   │   ├── register.cy.js
│   │   ├── forgot-password.cy.js
│   │   └── email-verification.cy.js
│   ├── admins/                  # Testes CRUD de Admins
│   │   └── admins-crud.cy.js
│   ├── franchises/              # Testes CRUD de Franchises
│   │   └── franchises-crud.cy.js
│   ├── clients/                 # Testes CRUD de Clients
│   │   └── clients-crud.cy.js
│   └── collaborators/           # Testes CRUD de Collaborators
│       └── collaborators-crud.cy.js
├── fixtures/                    # Dados de teste (JSON)
├── support/
│   ├── commands.js             # Comandos customizados
│   ├── e2e.js                  # Configuração global
│   └── helpers/                # Helper functions
└── downloads/                   # Arquivos baixados durante testes
```

## 🔧 Comandos Customizados

### Autenticação
```javascript
cy.login('email@example.com', 'password')
cy.loginAs('admin')  // admin, franchise, client, collaborator
cy.logout()
```

### Banco de Dados
```javascript
cy.resetDatabase()
cy.seedDatabase('CypressTestSeeder')
cy.artisan('migrate:fresh --seed')
```

### Navegação Inertia
```javascript
cy.visitInertia('/dashboard')
cy.waitForInertia()
```

### Formulários
```javascript
cy.fillForm({
  name: 'John Doe',
  email: 'john@example.com',
  password: 'secret'
})
```

### Validações
```javascript
cy.isAuthenticated()
cy.isNotAuthenticated()
cy.waitForToast('Success message')
```

## 👥 Usuários de Teste

Os seguintes usuários são criados pelo `CypressTestSeeder`:

| Role         | Email                      | Password |
|--------------|----------------------------|----------|
| Admin        | admin@maxcamrh.com         | password |
| Franchise    | franchise@maxcamrh.com     | password |
| Client       | client@maxcamrh.com        | password |
| Collaborator | collaborator@maxcamrh.com  | password |

## 🐛 Debugging

### Ver testes em modo interativo
```bash
npm run cypress:open
```

### Capturar screenshots em falhas
Screenshots são salvos automaticamente em `cypress/screenshots/`

### Gravar vídeos
Vídeos são salvos em `cypress/videos/` quando executado em modo headless

### Logs customizados
```javascript
cy.log('Debug message here')
```

## ⚙️ Configuração

### Arquivo de configuração
`cypress.config.js` - Configurações globais do Cypress

### Variáveis de ambiente
`.env.cypress` - Variáveis de ambiente específicas para testes

### Timeout padrão
- Comandos: 10s
- Requisições: 10s
- Carregamento de página: 30s

## 📊 Relatórios

### Relatórios no terminal
Ao executar `npm run cypress:run`, um relatório é exibido no terminal

### Screenshots de falhas
Automaticamente salvos em `cypress/screenshots/`

### Vídeos
Automaticamente gravados em `cypress/videos/` (modo headless)

## 🔄 CI/CD

Para rodar em CI/CD, use:

```bash
npm run test:e2e
```

Isso executa todos os testes em modo headless.

## 📝 Boas Práticas

1. **Reset do banco**: Sempre inicie testes com `cy.artisan('migrate:fresh --seed')`
2. **Isolamento**: Cada teste deve ser independente
3. **Seletores**: Use data-attributes ou IDs estáveis
4. **Esperas**: Use `cy.wait()` apenas quando necessário, prefira `should()`
5. **Comandos customizados**: Reutilize comandos para ações comuns
6. **Fixtures**: Use fixtures para dados de teste consistentes

## 🆘 Troubleshooting

### Erro: "baseUrl is not set"
Verifique se a aplicação está rodando em `http://localhost`

### Erro: "Timed out"
Aumente os timeouts em `cypress.config.js`

### Erro: "Database not found"
Execute as migrations: `./vendor/bin/sail artisan migrate:fresh --seed`

### Testes falhando aleatoriamente
- Adicione esperas explícitas
- Verifique se o banco está sendo resetado corretamente
- Use `cy.waitForInertia()` após navegações

## 🔗 Links Úteis

- [Documentação Cypress](https://docs.cypress.io)
- [Cypress Best Practices](https://docs.cypress.io/guides/references/best-practices)
- [Testing Library Cypress](https://testing-library.com/docs/cypress-testing-library/intro/)
