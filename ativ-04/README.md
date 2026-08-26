# Atividade 04 - Pipeline CI de Qualidade e Segurança (JavaScript)

Esta atividade configura uma pipeline de CI com GitHub Actions em JavaScript com as seguintes etapas de validação:

1. **Análise de Código (Linter)** - ESLint
2. **Testes Unitários** - Jest
3. **Cobertura de Código** - Jest (mínimo de 80%)
4. **Análise de Segurança** - Secret Scanning e npm audit

---

## Estrutura de Arquivos

```text
ativ-04/
├── eslint.config.js       # Configuração do ESLint
├── package.json           # Dependências e scripts da atividade
├── README.md              # Documentação e instruções do desafio
├── scripts/
│   └── check-secrets.js   # Script de varredura estática de segredos
├── src/
│   └── app.js             # Código-fonte da aplicação
└── tests/
    └── app.test.js        # Testes unitários Jest
```

---

## Execução Local

Para instalar as dependências e rodar as verificações localmente:

```bash
cd ativ-04
npm install
```

- **Linter**: `npm run lint`
- **Testes**: `npm test`
- **Cobertura**: `npm run test:coverage`
- **Segurança**: `npm run security:check`

---

## Desafio: Simulação dos Cenários de Falha

### 1. Erro de Lint
- **Como simular**: Em `src/app.js`, descomente a linha `const variavelSemUso = "erro_de_lint";`.
- **Resultado**: O comando `npm run lint` falhará informando que a variável foi declarada mas não utilizada.

### 2. Teste Unitário Reprovado
- **Como simular**: Em `tests/app.test.js`, altere `expect(soma(2, 3)).toBe(5);` para `toBe(999);`.
- **Resultado**: O comando `npm test` falhará na asserção.

### 3. Cobertura Abaixo do Limite Mínimo
- **Como simular**: Em `src/app.js`, descomente a função `funcaoSemTesteNaoCoberta`.
- **Resultado**: O comando `npm run test:coverage` falhará pois a cobertura ficará abaixo de 80%.

### 4. Chave de API Exposta
- **Como simular**: Em `src/app.js`, descomente a linha `const API_KEY_EXPOSTA = "sk_live_1234567890abcdef1234567890";`.
- **Resultado**: O comando `npm run security:check` indicará a presença de um segredo exposto e interromperá a execução.
