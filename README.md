# Integração e Entrega Contínua

Repositório da disciplina de **Integração e Entrega Contínua (CI/CD)** - 4º Semestre.

**Autora:** Emilly Budri Bognar  
**Repositório:** [`emillybudri/integracao-e-entrega-continua`](https://github.com/emillybudri/integracao-e-entrega-continua)

---

## Sobre o Repositório

Este repositório reúne os projetos, guias e atividades práticas desenvolvidas durante as aulas de **Integração e Entrega Contínua**. O foco principal abrange:

- Gerenciamento de configuração e controle de versão com **Git e GitHub**.
- Estratégias de branching e colaboração em equipe (fluxo Gitflow reduzido: `main` ← `dev` ← `feat/*`).
- Conceitos e práticas de **Integração Contínua (CI)** e **Entrega Contínua (CD)**.
- Automação de pipelines, testes automatizados e gestão de Pull Requests.

---

## Conteúdos e Atividades

### Atividade 01 (12/08/2026): Guia de Versionamento e Colaboração
Localizado em [`ativ-01/Guia-de-Versionamento-e-Colaboracao.md`](ativ-01/Guia-de-Versionamento-e-Colaboracao.md).

O guia aborda:
- **Fluxo de Trabalho Git & GitHub**: Passo a passo ilustrado desde a instalação, configuração (`git config`), clonagem e gerenciamento de branches.
- **Conceitos de CI/CD e Pipelines**: Explicação prática sobre quando as pipelines de Integração Contínua (CI) e Entrega Contínua (CD) entram em ação durante o desenvolvimento.
- **Pull Requests e Code Review**: Padrões para abertura de PRs, revisão de código e mesclagem de branches.
- **Operações Avançadas e Resolução de Conflitos**: Uso de `rebase`, `amend`, `reset`, `restore`, `revert`, `stash` e `cherry-pick`.
- **Guia Rápido de Comandos**: Resumo consolidado de comandos do terminal.

### Atividade 02 (19/08/2026): Calculadora JavaScript, Testes Automatizados com Jest e CI GitHub Actions
Localizado na pasta [`ativ-02/`](ativ-02/).

A atividade consiste em uma aplicação de calculadora em JavaScript (Node.js) executada via terminal, com cobertura de testes unitários em Jest e automação de CI no GitHub Actions:

- **Execução no Terminal ([`ativ-02/index.js`](ativ-02/index.js))**: Interface interativa via linha de comando (CLI) construída com o módulo `readline`.
- **Lógica Matemática ([`ativ-02/calculator.js`](ativ-02/calculator.js))**: Módulo central contendo as 4 operações básicas (`soma`, `subtracao`, `multiplicacao`, `divisao`) com tratamento de divisão por zero.
- **Suíte de Testes Automatizados ([`ativ-02/tests/`](ativ-02/tests/))**: casos de teste com **Jest** divididos em arquivos por operação:
  - `tests/soma.test.js`
  - `tests/subtracao.test.js`
  - `tests/multiplicacao.test.js`
  - `tests/divisao.test.js`
- **Esteira de CI no GitHub Actions ([`.github/workflows/ativ-02-pipeline.yml`](.github/workflows/ativ-02-pipeline.yml))**: Pipeline configurada em `ubuntu-latest` que roda 4 jobs independentes em paralelo (`teste-soma`, `teste-subtracao`, `teste-multiplicacao`, `teste-divisao`) a cada `push` para a branch `main`.

#### Como Executar

1. **Navegar até a pasta da atividade**:
   ```bash
   cd ativ-02
   ```

2. **Instalar as dependências**:
   ```bash
   npm install
   ```

3. **Executar a Calculadora no Terminal**:
   ```bash
   npm start
   # ou: node index.js
   ```

4. **Executar os Testes Automatizados (Jest)**:
   - **Todos os testes**: `npm test`
   - **Teste de Soma**: `npm run test:soma`
   - **Teste de Subtração**: `npm run test:subtracao`
   - **Teste de Multiplicação**: `npm run test:multiplicacao`
   - **Teste de Divisão**: `npm run test:divisao`

---

## Fluxo de Versionamento Adotado

```text
main (Ambiente Estável / Produção)
  ↑
 dev (Ambiente de Integração / Staging)
  ↑
feat/minha-feature (Desenvolvimento da Funcionalidade)
```

1. **Desenvolvimento**: As novas funcionalidades são construídas em branches do tipo `feat/*` criadas a partir da branch `dev`.
2. **Integração (CI)**: Ao abrir o Pull Request da branch `feat/*` para `dev`, a pipeline de CI executa os testes automatizados.
3. **Revisão**: O código passa por Code Review antes da mesclagem na `dev`.
4. **Entrega (CD)**: O merge na branch `dev` dispara o deploy em ambiente de homologação. Após validação, o código segue para `main` via novo PR.

---

## Licença

Este projeto é desenvolvido para fins acadêmicos na disciplina de Integração e Entrega Contínua.
