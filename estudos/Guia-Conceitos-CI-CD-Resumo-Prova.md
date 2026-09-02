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

## ⚡ 7. Caching de Dependências em CI/CD

### 🚨 O Problema Sem Cache
Ao rodar múltiplos jobs (como em uma matriz com 6 ambientes), cada máquina virtual inicia **do zero** e precisa baixar e instalar todas as dependências da internet via `pip` ou `npm`. Isso gera:
- **Lenteza**: Vários minutos perdidos rebaixando os mesmos arquivos em cada execução.
- **Gasto desnecessário de rede e minutos**: Consumo desnecessário da cota do GitHub Actions.
- **Risco de falhas externas**: Se o PyPI ou npm ficarem fora do ar momentaneamente, a pipeline inteira quebra.

### 💾 O que é Cache e Como Funciona?
- **Conceito**: É o armazenamento temporário em nuvem dos pacotes já baixados para reutilização em execuções futuras.
- **Como funciona**:
  1. **Primeira Execução**: A máquina baixa os pacotes da internet, instala e salva o diretório no **Cache** do GitHub.
  2. **Execuções Seguintes**: O GitHub Actions reconhece os pacotes salvos no Cache, pula o download externo e **restaura tudo em 2 a 3 segundos**.

### 💻 Como Usar no GitHub Actions (`cache: 'pip'`)
Nas actions oficiais como `actions/setup-python` ou `actions/setup-node`, basta adicionar o parâmetro `cache`:

```yaml
- name: Configurar Python
  uses: actions/setup-python@v5
  with:
    python-version: ${{ matrix.python-version }}
    cache: 'pip' # Habilita o cache automático das dependências do pip
```

### 📈 Ganhos ao Utilizar Cache
1. **Velocidade**: A etapa de instalação passa de minutos para escassos segundos.
2. **Economia Financeira**: Reduz consideravelmente os minutos consumidos dos runners pagos.
3. **Resiliência**: Garante a execução da pipeline mesmo se repositórios públicos externos apresentarem lentidão.

---

## 📦 8. Gerenciamento de Dependências com `requirements.txt`

### 📄 O que é o `requirements.txt`?
O `requirements.txt` é um arquivo de texto simples usado no ecossistema Python que lista todas as bibliotecas e pacotes externos (com suas respectivas versões) que a sua aplicação necessita para ser executada.

### 🎯 Por que ele é fundamental em CI/CD?
1. **Reprodutibilidade do Ambiente**: Garante que o projeto rode **exatamente com as mesmas versões de bibliotecas** em qualquer computador ou máquina virtual do GitHub Actions.
2. **Integração com o Cache no GitHub Actions**:
   - O `actions/setup-python@v5` calcula um **hash** (identificador único) do conteúdo do `requirements.txt`.
   - Se o arquivo não mudou entre as execuções, o GitHub Actions restaura os pacotes direto do Cache em segundos.
   - Se você adicionar um novo pacote no `requirements.txt`, o hash muda automaticamente, fazendo o CI/CD baixar a nova dependência e atualizar o Cache.

### 💻 Como Usar Localmente
- **Instalar dependências registradas no arquivo**:
  ```bash
  pip install -r requirements.txt
  ```
- **Gerar ou atualizar o arquivo com os pacotes atuais do ambiente**:
  ```bash
  pip freeze > requirements.txt
  ```

### ⚙️ Como Usar no Workflow do GitHub Actions
No workflow, indicamos a localização do arquivo com `cache-dependency-path` e realizamos a instalação via `-r`:

```yaml
- name: Configurar Python
  uses: actions/setup-python@v5
  with:
    python-version: ${{ matrix.python-version }}
    cache: 'pip'
    cache-dependency-path: '**/requirements.txt' # Localiza automaticamente o arquivo de requisitos

- name: Instalar dependências
  run: pip install -r ativ-05/requirements.txt # Instala via arquivo de requisitos
```

---

## 🛡️ 9. Aba "Security and Quality" e Recursos de Segurança do GitHub

A aba **Security and Quality** (Segurança e Qualidade) do repositório no GitHub reúne ferramentas nativas e automáticas para proteger o código-fonte, dependências e credenciais.

```text
GitHub Repository ──► [ Security and quality ]
                        ├── Dependabot (Malware & Vulnerabilidades de Bibliotecas)
                        ├── Code Scanning (CodeQL - SAST Análise de Bugs no Código)
                        ├── Secret Scanning (Detecção de Senhas e Tokens Expostos)
                        ├── Private Vulnerability Reporting (Relatórios Privados)
                        └── Security Policy & Advisories (Políticas e Avisos)
```

### 1️⃣ Dependabot (Vulnerabilidades de Dependências)
- **Alerts**: Notifica automaticamente quando uma biblioteca declarada em arquivos de dependências (como `package.json`, `requirements.txt` ou `pom.xml`) possui uma vulnerabilidade de segurança conhecida (CVE).
- **Security Updates**: Abre Pull Requests automáticos atualizando a biblioteca para a versão corrigida e segura.
- **Malware & Vulnerabilities**: Varre o ecossistema de pacotes em busca de código malicioso injetado por hackers em bibliotecas públicas.

### 2️⃣ Code Scanning (CodeQL / SAST)
- **O que é?** Ferramenta de **SAST (Static Application Security Testing)** nativa do GitHub.
- **Como funciona?** Analisa o código-fonte procurando por falhas graves de segurança (ex: SQL Injection, Cross-Site Scripting - XSS, vazamento de memória e erros de lógica).

### 3️⃣ Secret Scanning & Push Protection
- **Secret Scanning**: Rastria commits e historicos do Git em busca de credenciais expostas (como chaves da AWS, tokens do GitHub, chaves de API).
- **Push Protection**: Recurso que bloqueia o `git push` **antes** que o commit seja aceito no repositório se detectar um segredo válido, impedindo o vazamento antes mesmo dele acontecer.

### 4️⃣ Private Vulnerability Reporting & Security Policy
- **Private Vulnerability Reporting**: Permite que pesquisadores externos de segurança relatem falhas diretamente e de forma privada aos mantenedores do projeto, sem criar Issues públicas que exporiam a falha.
- **Security Policy (`SECURITY.md`)**: Arquivo de política no repositório que orienta a comunidade sobre como reportar vulnerabilidades de forma responsável.

---

## 🔑 10. Secrets, Variables, Environments e `.env` no GitHub

### 🔒 1. Actions Secrets (`${{ secrets.NOME_DO_SEGREDO }}`)
- **O que é?** Valores sensíveis e criptografados (senhas de banco, tokens de API, chaves SSH, `SONAR_TOKEN`).
- **Onde fica no GitHub?** `Settings` ➔ `Secrets and variables` ➔ `Actions`.
- **Segurança**: Os valores nunca são exibidos nos logs de execução do GitHub Actions. Se tentarem imprimir o segredo com `echo`, o GitHub substitui automaticamente por `***`.

### 📌 2. Actions Variables (`${{ vars.NOME_DA_VARIAVEL }}`)
- **O que é?** Valores de configuração **não sensíveis** reutilizáveis entre os workflows (ex: URLs de APIs públicas, porta do servidor, nome do ambiente).

### 🌍 3. Environments (Ambientes: Staging, Production)
- **O que é?** Representa os ambientes reais de destino do software (ex: `staging`, `production`).
- **Recursos dos Environments**:
  - **Secrets de Ambiente**: Permite ter um segredo chamado `DATABASE_URL` diferente para produção e para homologação.
  - **Deployment Protection Rules**: Regras de proteção que exigem **aprovação manual** de um revisor ou aprovação de testes específicos antes de liberar o deploy em um ambiente crítico.

### 📄 4. O Arquivo `.env` vs Bloco `env:` na Pipeline
- **Arquivo `.env` Local**: Usado apenas na máquina do desenvolvedor para rodar a aplicação localmente. **NUNCA deve ser commitado no Git** (deve constar obrigatoriamente no `.gitignore`).
- **Bloco `env:` no GitHub Actions**: É a sintaxe em YAML para disponibilizar segredos ou variáveis aos passos e scripts da pipeline:

```yaml
steps:
  - name: Executar script com variáveis
    env:
      DATABASE_URL: ${{ secrets.PROD_DB_URL }} # Injeta o segredo como variável de ambiente
      NODE_ENV: 'production'
    run: npm start
```

---

## ☁️ 11. SonarCloud: Análise de Qualidade e Segurança de Código

### 🔍 O que é o SonarCloud?
O **SonarCloud** é uma plataforma baseada em nuvem para **Análise Estática de Código (SAST)** e avaliação da **qualidade de software**. Ele analisa o código a cada commit/PR e gera relatórios detalhados com indicadores de saúde do projeto.

### 📊 O que o SonarCloud analisa?
1. **Bugs**: Erros no código que farão a aplicação quebrar em execução.
2. **Vulnerabilidades**: Falhas de segurança que abrem brechas para ataques.
3. **Security Hotspots**: Trechos de código sensíveis que exigem revisão manual de um humano.
4. **Code Smells (Maus Cheiros)**: Código confuso, mal formatado ou difícil de manter (débito técnico).
5. **Duplicações de Código**: Trechos de código copiados e colados em vários lugares.
6. **Quality Gate (Portão de Qualidade)**: Conjunto de regras de aprovação. Se o código não atingir os critérios (ex: mínimo 80% de cobertura e 0 vulnerabilidades), o Quality Gate **reprova o Pull Request**.

---

### 🚀 Passo a Passo: Como Integrar o SonarCloud na Pipeline do GitHub Actions

```text
[ Desenvolvedor envia PR ] ──► [ GitHub Actions executa testes ]
                                              │
                                              ▼
[ SonarCloud analisa o código ] ◄── [ SonarScanner envia código ]
              │
              ▼
[ Avaliação do Quality Gate ] ──► (Aprovado ou Reprovado no GitHub)
```

#### Passo 1: Criar Conta e Importar o Repositório no SonarCloud
1. Acesse [sonarcloud.io](https://sonarcloud.io) e faça login com sua conta do GitHub.
2. Clique em **Import an organization** e selecione o repositório do GitHub.

#### Passo 2: Gerar o Token de Acesso
1. No SonarCloud, vá em **My Account ➔ Security**.
2. Gere um novo token de acesso (ex: chamando de `github-actions-token`).

#### Passo 3: Cadastrar o Segredo no GitHub
1. No seu repositório no GitHub, vá em **Settings ➔ Secrets and variables ➔ Actions**.
2. Clique em **New repository secret**.
3. **Nome**: `SONAR_TOKEN`
4. **Valor**: Cole o token gerado no SonarCloud.

#### Passo 4: Criar o Arquivo `sonar-project.properties` na Raiz do Projeto
Crie o arquivo `sonar-project.properties` no seu repositório especificando as chaves da sua organização e projeto:

```properties
sonar.organization=sua-organizacao-sonar
sonar.projectKey=sua-organizacao_seu-repositorio
sonar.sources=.
sonar.exclusions=**/node_modules/**,**/tests/**
sonar.tests=.
sonar.test.inclusions=**/*.test.js,**/test_*.py
sonar.python.version=3.12
```

#### Passo 5: Adicionar o Job do SonarCloud no Workflow do GitHub Actions (`.github/workflows/sonar.yml`)

```yaml
name: Analise de Qualidade - SonarCloud

on:
  push:
    branches: [main, dev]
  pull_request:
    types: [opened, synchronize, reopened]

jobs:
  sonarcloud:
    name: Analise do SonarCloud
    runs-on: ubuntu-latest
    steps:
      - name: Baixar Código do Repositório
        uses: actions/checkout@v4
        with:
          fetch-depth: 0 # O SonarCloud precisa de todo o histórico do Git para atribuir o autor das linhas

      - name: Executar SonarCloud Scanner
        uses: SonarSource/sonarcloud-github-action@master
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
          SONAR_TOKEN: ${{ secrets.SONAR_TOKEN }}
```

---

## 📝 Workflows Práticos no Repositório

- **[Workflow 01 - Estrutura Padrão](../.github/workflows/01-estrutura-padrao.yml)**: Estudo da sintaxe fundamental YAML.
- **[Workflow 02 - Conceitos Avançados](../.github/workflows/02-estudo-conceitos-avancados.yml)**: Aplicação prática de Paralelo, Sequencial, DevSecOps, Matrix e E2E.
- **[Workflow Atividade 05](../.github/workflows/ativ-05-pipeline-matrix-py.yml)**: Pipeline em Matrix com 6 combinações utilizando `cache: 'pip'` e `requirements.txt`.
