# 📚 Guia de Conceitos Avançados de CI/CD - Resumo para Prova

Este documento reúne explicações detalhadas, definições, casos de uso, ordem de execução (paralelo vs sequencial) e exemplos práticos dos principais conceitos de Integração e Entrega Contínua (CI/CD) com GitHub Actions.

---

## 🔗 Links Diretos para os Workflows de Estudo

- 📄 **[Workflow 01 - Estrutura Padrão (Sintaxe Básica YAML)](../.github/workflows/01-estrutura-padrao.yml)**: Guia prático com explicações linha por linha das chaves fundamentais do GitHub Actions.
- 📄 **[Workflow 02 - Conceitos Avançados (DevSecOps, Matrix, E2E)](../.github/workflows/02-estudo-conceitos-avancados.yml)**: Exemplo prático aplicando ordem de execução, Gitleaks, Linter, Matrix e Testes E2E.

---

## 📌 0. Resumo da Sintaxe Básica do GitHub Actions (Base do Workflow 01)

Antes dos conceitos avançados, é fundamental dominar as chaves básicas da sintaxe YAML do GitHub Actions aprendidas no **[Workflow 01](../.github/workflows/01-estrutura-padrao.yml)**:

- `name`: Nome do workflow que aparece na interface gráfica da aba *Actions* do GitHub.
- `on`: Define os **eventos/gatilhos** (triggers) que disparam a automação (ex: `push`, `pull_request`).
  - `branches`: Filtra as branches específicas (ex: `[main, dev]`).
- `jobs`: Agrupa o conjunto de tarefas que serão executadas. Cada job tem seu próprio identificador (ex: `teste:`).
- `runs-on`: Especifica o sistema operacional do ambiente isolado/máquina virtual (Runner) onde o job executa (ex: `ubuntu-latest`, `windows-latest`).
- `steps`: Lista sequencial de passos a serem executados dentro de um job.
  - `name`: Rótulo descritivo de cada passo para exibição nos logs.
  - `uses`: Executa uma *Action* pronta do GitHub Marketplace (ex: `actions/checkout@v4`, `actions/setup-python@v5`).
  - `with`: Passa parâmetros extras de configuração para a action especificada no `uses` (ex: `python-version: "3.12"`).
  - `run`: Executa comandos diretos de terminal (shell script) no SO da máquina virtual (ex: `pip install pytest`).
  - `working-directory`: Define o diretório (pasta) específico onde os comandos do `run` serão executados no projeto.

---

## ⏱️ 1. Execução: Paralelo vs Sequencial

### 🔁 Execução Sequencial
- **O que é?** As tarefas são executadas **uma por uma**, em ordem linear. A próxima etapa só inicia após a anterior terminar com sucesso.
- **Como funciona no GitHub Actions?**
  - **Steps (Passos)** dentro de um mesmo job **sempre rodam em sequência**.
  - **Jobs** rodam em sequência quando utilizamos a diretiva `needs: [nome-do-job-anterior]`.
- **Quando utilizar?** Quando um job depende diretamente do resultado de outro. Exemplo: O job de *Deploy* só deve rodar se o job de *Testes* passar.

### ⚡ Execução em Paralelo
- **O que é?** Múltiplas tarefas são executadas **ao mesmo tempo**, em máquinas virtuais (runners) separadas e independentes.
- **Como funciona no GitHub Actions?**
  - Por padrão, todos os **Jobs** que não possuem `needs` rodam em paralelo.
  - A estratégia de **Matrix** (`strategy: matrix`) dispara as variações em paralelo automaticamente.
- **Quando utilizar?** Para reduzir o tempo total de execução da pipeline. Exemplo: Rodar Linter, Segurança e Testes ao mesmo tempo.

---

## 🎯 2. Ordem de Execução Recomendada na Pipeline

Para otimizar o tempo e reduzir custos de execução no GitHub Actions:

```text
[ Gitleaks / Segurança ]  ─┐
[ Análise de Código (Lint) ] ├─► Executam em PARALELO no início (rápidos)
[ Testes Unitários ]       ─┘
         │
         ▼ (se todos passarem)
[ Testes de Integração / Cobertura ] ──► Sequencial ou Paralelo controlado
         │
         ▼ (se tudo passar)
[ Testes E2E (End-to-End) ] ─────────► Executado por último (lento e custoso)
         │
         ▼ (se aprovar)
[ Deploy ] ──────────────────────────► Execução Final (depende de todos os anteriores)
```

---

## 🔬 3. Tipos de Testes e Análises

### 🧪 Testes Unitários
- **Conceito**: Testam a menor unidade isolada de código (função, método ou classe) em memória, sem depender de banco de dados ou APIs externas.
- **Quando utilizar?** Em todos os commits e Pull Requests. São ultra-rápidos e baratos.
- **Ordem**: No início da esteira.

### 🔗 Testes de Integração e E2E (End-to-End)
- **Testes de Integração**: Testam a comunicação entre dois ou mais módulos (ex: aplicação + banco de dados).
- **Testes E2E (End-to-End)**: Simulam o fluxo completo do usuário do início ao fim (navegador, telas, APIs reais).
- **Quando utilizar?** Em branches de integração (`dev`, `main`) antes do deploy. São mais lentos e custosos.
- **Ordem**: Após o sucesso dos testes unitários.

### 📊 Code Coverage (Cobertura de Código)
- **Conceito**: Métrica percentual (%) que indica quanto do código-fonte foi executado durante a bateria de testes.
- **Quando utilizar?** Para garantir padrões de qualidade (ex: exigir no mínimo 80% de cobertura no PR).
- **Ordem**: Durante ou logo após a execução dos testes unitários.

### 🔍 Análise de Código (Linter / Análise Estática)
- **Conceito**: Ferramenta (ex: ESLint, Flake8, Ruff) que examina o código sem executá-lo para detectar erros de sintaxe, código morto e padronização de estilo.
- **Quando utilizar?** Como primeira etapa da pipeline para barrar problemas simples imediatamente.

---

## 🛡️ 4. DevSecOps & Gitleaks (Análise de Segurança)

### 🔐 O que é DevSecOps?
- **Conceito**: Integração da **segurança** em todas as fases do ciclo de desenvolvimento (CI/CD), em vez de tratar a segurança apenas no final.

### 🔑 Gitleaks / Secret Scanning
- **O que faz?** Varre o histórico do Git e os arquivos do projeto em busca de **senhas, chaves de API, tokens e certificados** expostos acidentalmente.
- **Quando utilizar?** No primeiro job do workflow ou via *pre-commit hooks*. Se um segredo for detectado, o pipeline falha imediatamente bloqueando o commit/PR.

---

## 🔀 5. Conceito de `strategy` e `matrix`

### 💡 O que é Matrix?
- **Conceito**: Recurso do GitHub Actions que permite criar múltiplos jobs automaticamente combinando diferentes variáveis (sistemas operacionais, versões de linguagens, etc.).

### 💡 "Não matar barata com canhão" (Otimização da Matrix)
- **O que significa?** Evitar matrizes gigantescas desnecessárias (ex: 5 SOs × 6 versões do Python = 30 jobs) que gastam minutos e créditos do GitHub Actions sem necessidade.
- **Exemplo prático**: Testar em **2 Sistemas Operacionais** (`ubuntu-latest` e `windows-latest`) e nas versões principais suportadas do Python (ex: `3.11` e `3.12`).

```yaml
strategy:
  matrix:
    os: [ubuntu-latest, windows-latest] # 2 SOs principais para não matar barata com canhão
    python-version: ['3.11', '3.12']
```

---

## 💡 6. Entendendo do Básico & Síntese da Ordem de Execução

### 🛑 O Básico com Analogia da Cozinha
- **Sequencial**: Imagine preparar uma pizza. Você precisa abrir a massa, colocar o recheio e só então assar. A ordem é obrigatória e um passo depende do anterior.
- **Paralelo**: Imagine uma equipe na cozinha. Enquanto um cozinheiro pica os legumes, outro prepara a sobremesa. Ambos trabalham ao mesmo tempo para economizar tempo total.

### 🎯 Síntese: Por que a Ordem da Pipeline é Escolhida Assim?

1. **Filtragem Rápida e Barata (Fail-Fast)**: Linter e Gitleaks rodam primeiro (em paralelo) porque levam poucos segundos. Se houver um erro bobo de sintaxe ou vazamento de senha, a pipeline falha em 3 segundos sem gastar máquinas com testes mais pesados.
2. **Dependência Lógica**: Testes unitários (rápidos e em memória) rodam antes dos testes de Integração/E2E (lentos e pesados). Não faz sentido testar telas e navegadores se a lógica interna do código falhou.
3. **Segurança no Deploy**: O *Deploy* é a última etapa sequencial (`needs: [todos-os-jobs]`), garantindo que apenas código 100% testado e seguro chegue aos usuários.

---

## 📝 Workflows Práticos no Repositório

- **[Workflow 01 - Estrutura Padrão](../.github/workflows/01-estrutura-padrao.yml)**: Estudo da sintaxe fundamental YAML.
- **[Workflow 02 - Conceitos Avançados](../.github/workflows/02-estudo-conceitos-avancados.yml)**: Aplicação prática de Paralelo, Sequencial, DevSecOps, Matrix e E2E.
