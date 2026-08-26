# Integração e Entrega Contínua (CI/CD)

Repositório oficial da disciplina de **Integração e Entrega Contínua** - 4º Semestre.

**Autora:** Emilly Budri Bognar  
**Repositório:** [`emillybudri/integracao-e-entrega-continua`](https://github.com/emillybudri/integracao-e-entrega-continua)

---

## 📌 Sobre o Repositório

Este repositório reúne os estudos, atividades práticas e pipelines de automação desenvolvidas na disciplina de **Integração e Entrega Contínua (CI/CD)**. O objetivo é aplicar conceitos de controle de versão, esteiras de automação com GitHub Actions, testes automatizados e estratégias de entrega contínua.

---

## 📚 Material de Estudo e Resumo para Prova

### 📝 [`01-estrutura-padrao.yml`](.github/workflows/01-estrutura-padrao.yml) - Estrutura Padrão de Workflow
- **Finalidade**: Anotação essencial e guia de estudos com explicações detalhadas linha a linha sobre a sintaxe YAML e a estrutura básica de um workflow do GitHub Actions (`name`, `on`, `jobs`, `runs-on`, `steps`, `uses`, `with`, `run`).
- **Arquivo de Teste Relacionado**: [`estudos/test_soma.py`](estudos/test_soma.py) (teste unitário em Python executado pelo workflow no diretório `estudos/`).
- **Linguagem demonstrativa**: Python 3.12 com Pytest.

---

## 🛠️ Atividades Práticas e Workflows (Linha do Tempo)

### 🚀 Atividade 01 (19/08/2026) - Primeira Pipeline de CI com Python
- **Código e Documentação**: Pasta [`ativ-01/`](ativ-01/)
  - [`Guia-de-Versionamento-e-Colaboracao.md`](ativ-01/Guia-de-Versionamento-e-Colaboracao.md): Manual sobre Git, GitHub, branching (`main` ← `dev` ← `feat/*`), Pull Requests, Code Review e resolução de conflitos.
  - [`test_exemplo.py`](ativ-01/test_exemplo.py): Teste unitário automatizado em Python da Atividade 01.
- **Workflow de CI**: [`.github/workflows/ativ-01-primeira-pipeline-py.yml`](.github/workflows/ativ-01-primeira-pipeline-py.yml)
  - **Nome no GitHub Actions**: `Atividade 01 - Primeira Pipeline de CI com Python (19/08/2026)`
  - **Tecnologia**: Python 3.12 (`pytest`).
  - **Estrutura**: Job de teste automatizado seguido por job condicional de deploy.

---

### 🧮 Atividade 02 (19/08/2026) - Pipeline Calculadora de CI com JavaScript
- **Código Fonte**: Pasta [`ativ-02/`](ativ-02/)
  - Aplicação interativa em Node.js (`index.js` e `calculator.js`) com testes automatizados em **Jest** (`tests/`).
- **Workflow de CI**: [`.github/workflows/ativ-02-pipeline-calculadora-js.yml`](.github/workflows/ativ-02-pipeline-calculadora-js.yml)
  - **Nome no GitHub Actions**: `Atividade 02 - Pipeline Calculadora de CI com JavaScript (19/08/2026)`
  - **Tecnologia**: Node.js v22 (`npm install`, `Jest`).
  - **Estrutura**: Execução paralela de 4 jobs independentes de teste (`teste-soma`, `teste-subtracao`, `teste-multiplicacao`, `teste-divisao`), finalizando no job de `deploy`.

---

### ⚡ Atividade 03 (26/08/2026) - Pipeline na Linguagem Favorita -> JavaScript
- **Workflow de CI**: [`.github/workflows/ativ-03-pipeline-linguagem-favorita-js.yml`](.github/workflows/ativ-03-pipeline-linguagem-favorita-js.yml)
  - **Nome no GitHub Actions**: `Atividade 03 - Pipeline na Linguagem Favorita -> JavaScript (26/08/2026)`
  - **Tecnologia**: Node.js v22 (`npm install`, `npm test`).
  - **Estrutura**: Estrutura padrão em JavaScript com comentários explicativos sobre comandos e o uso do operador multi-linha (`|`).

---

### Atividade 04 (26/08/2026) - Pipeline CI de Qualidade e Segurança (JavaScript)
- **Código e Documentação**: Pasta [`ativ-04/`](ativ-04/)
  - [`README.md`](ativ-04/README.md): Guia da atividade e instruções para simulação dos 4 cenários de falha (Lint, Testes, Cobertura, Segredos).
  - [`src/app.js`](ativ-04/src/app.js) e [`tests/app.test.js`](ativ-04/tests/app.test.js): Aplicação e testes unitários em Jest.
  - [`scripts/check-secrets.js`](ativ-04/scripts/check-secrets.js): Script de segurança estática para varredura de credenciais e chaves expostas.
- **Workflow de CI**: [`.github/workflows/ativ-04-pipeline-qualidade-seguranca-js.yml`](.github/workflows/ativ-04-pipeline-qualidade-seguranca-js.yml)
  - **Nome no GitHub Actions**: `Atividade 04 - Pipeline CI com JavaScript (26/08/2026)`
  - **Tecnologia**: Node.js v22 (`ESLint`, `Jest`, `Jest Coverage >= 80%`, `Secret Scanning`).
  - **Estrutura**: Esteira com 4 validações (`analise-linter`, `testes-unitarios`, `cobertura-codigo`, `analise-seguranca`) executando antes do job de `deploy`.

---

## 🗂️ Estrutura do Repositório

```text
integracao-e-entrega-continua/
├── .github/
│   └── workflows/
│       ├── 01-estrutura-padrao.yml                       # Estudo & Resumo para Prova
│       ├── ativ-01-primeira-pipeline-py.yml              # Atividade 01 (19/08/2026)
│       ├── ativ-02-pipeline-calculadora-js.yml           # Atividade 02 (19/08/2026)
│       ├── ativ-03-pipeline-linguagem-favorita-js.yml    # Atividade 03 (26/08/2026)
│       └── ativ-04-pipeline-qualidade-seguranca-js.yml   # Atividade 04 (26/08/2026)
├── ativ-01/
│   ├── Guia-de-Versionamento-e-Colaboracao.md
│   └── test_exemplo.py                                   # Teste em Python da Atividade 01
├── ativ-02/
│   ├── calculator.js
│   ├── index.js
│   ├── package.json
│   └── tests/
│       ├── soma.test.js
│       ├── subtracao.test.js
│       ├── multiplicacao.test.js
│       └── divisao.test.js
├── ativ-04/
│   ├── eslint.config.js                                  # Configuração do Linter (ESLint)
│   ├── package.json                                      # Scripts e limite de cobertura (80%)
│   ├── README.md                                         # Guia da Atividade 04 e Desafio
│   ├── scripts/
│   │   └── check-secrets.js                              # Scanner de chaves de API expostas
│   ├── src/
│   │   └── app.js                                        # Aplicação em JS
│   └── tests/
│       └── app.test.js                                   # Testes unitários Jest
├── estudos/
│   └── test_soma.py                                     # Teste em Python do Estudo 01
└── README.md
```

---

## 🔄 Fluxo de Git e Branching Adotado

```text
main (Ambiente Estável / Produção)
  ↑
 dev (Ambiente de Integração / Staging)
  ↑
feat/minha-feature (Desenvolvimento da Funcionalidade)
```

1. **Desenvolvimento**: Branches `feat/*` criadas a partir da branch `dev`.
2. **Integração (CI)**: Pull Requests para `dev` ou `main` disparam a validação dos workflows no GitHub Actions.
3. **Revisão e Merge**: Mesclagem realizada após aprovação e sucesso de todos os jobs da pipeline.

---

## 📄 Licença

Projeto desenvolvido com fins acadêmicos para a disciplina de Integração e Entrega Contínua.
