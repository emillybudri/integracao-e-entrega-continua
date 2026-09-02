# 📚 Guia Definitivo e Completo de Integração e Entrega Contínua (CI/CD) - Resumo Mestre para Prova

Este documento é o **resumo oficial unificado** de todo o conteúdo da disciplina de Integração e Entrega Contínua (Aulas 01 a 05). Ele reúne 100% da teoria, definições, comandos Git, sintaxe do GitHub Actions, padrões de testes, DevSecOps, Caching, Secrets, a analogia da cozinha e o passo a passo completo do SonarCloud.

---

## 🔗 Navegação Rápida para os Workflows do Repositório

- 📄 **[Workflow 01 - Estrutura Padrão (Sintaxe Básica YAML)](../.github/workflows/01-estrutura-padrao.yml)**: Estudo prático com explicações linha por linha das chaves fundamentais.
- 📄 **[Workflow 02 - Conceitos Avançados (DevSecOps, Matrix, E2E)](../.github/workflows/02-estudo-conceitos-avancados.yml)**: Aplicação prática da pirâmide de testes, Gitleaks, Linter, Matrix e E2E.
- 📄 **[Workflow Atividade 05 (Matrix & Cache)](../.github/workflows/ativ-05-pipeline-matrix-py.yml)**: Execução prática de Matrix de 6 ambientes com `cache: 'pip'` e `requirements.txt`.

---

## 📌 0. Resumo da Sintaxe Básica do GitHub Actions (Base do Workflow 01)

As chaves fundamentais da sintaxe YAML do GitHub Actions aprendidas no **[Workflow 01](../.github/workflows/01-estrutura-padrao.yml)**:

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

## 🏛️ 1. Introdução à CI/CD, Histórico e Cultura DevOps (Aula 01)

### ⏳ A Evolução Histórica do Desenvolvimento de Software

1. **Modelo Tradicional / Cascata (Antes dos anos 2000)**:
   - **Características**: Processo estritamente sequencial (Requerimentos ➔ Projeto ➔ Implementação ➔ Verificação/Testes ➔ Manutenção). A integração e os testes ocorriam apenas no final do projeto.
   - **Problema**: Erros eram descobertos muito tarde. O custo de correção no final era **exponencialmente mais alto**.
2. **Movimento Ágil (2001)**:
   - **Mudança de Mentalidade**: Foco em entregas menores e frequentes, feedback rápido, colaboração e software funcionando como prioridade máxima.
3. **Surgimento de CI/CD e DevOps (2009+)**:
   - **Motivação**: Com o crescimento dos sistemas web, o deploy manual virou o grande gargalo da engenharia. O erro humano na implantação era constante.
   - **Solução**: Automatizar a integração e a entrega de software para eliminar gargalos e reduzir riscos.

---

### 💡 Conceitos Fundamentais: CI vs. CD vs. Implantação Contínua

| Conceito | Definição | Onde termina? | Intervenção Humana? |
| :--- | :--- | :--- | :--- |
| **CI (Continuous Integration)** | Prática de integrar o código frequentemente a um repositório compartilhado, validando a cada commit por build e testes automatizados. | Termina na validação dos testes e qualidade. | Nenhuma (100% automatizado). |
| **Continuous Delivery (Entrega Contínua)** | Prática que garante que o software esteja **sempre pronto para ir para produção** a qualquer momento. | Gera artefato, constrói imagem Docker e envia para homologação. | **SIM** (exige aprovação humana para apertar o botão de Deploy em produção). |
| **Continuous Deployment (Implantação Contínua)** | Todo código aprovado nos testes automatizados vai **direto para produção automaticamente**. | Publica a aplicação em produção. | **NÃO** (0% intervenção humana, como na Netflix e Meta/Facebook). |

> 📌 **CONCEITO-CHAVE PARA A PROVA**:
> - **CI e CD são Práticas/Metodologias**.
> - **Pipeline é a implementação técnica (o mecanismo automatizado)** dessas práticas.

---

### ♾️ O que é DevOps e seus 3 Pilares
**DevOps** é a união entre **pessoas, processos e automação** para entregar software com frequência, qualidade e segurança. Resolve o conflito histórico onde *Dev culpava Ops e Ops culpava Dev*.

- **Os 3 Pilares do DevOps**:
  1. 🤝 **Cultura**: Colaboração, responsabilidade compartilhada e comunicação entre times.
  2. ⚙️ **Automação**: Automação de builds, testes, deploys e provisionamento de infraestrutura.
  3. 📊 **Medição e Melhoria Contínua**: Monitoramento de métricas, coleta de logs e feedback constante.

---

## 🌿 2. Gerenciamento de Configuração e Controle de Versão com Git (Aula 02)

### 📦 Gerenciamento de Configuração vs. Controle de Versão
- **Gerenciamento de Configuração**: Conjunto de práticas para identificar, controlar, versionar e acompanhar alterações em **todos os itens do sistema** (código-fonte, arquivos de configuração, documentação, scripts e infraestrutura).
- **Controle de Versão**: Sistema especializado em registrar o histórico de mudanças no código-fonte, permitindo colaboração e recuperação de versões anteriores.
- **Git**: Sistema de controle de versão distribuído, gratuito e open-source criado por **Linus Torvalds em 2005** para gerenciar o desenvolvimento do Kernel Linux.

---

### 🔄 O Ciclo Básico do Git e Estados de Arquivos

```text
[ Working Directory ] ──► (git add) ──► [ Staging Area ] ──► (git commit) ──► [ Repository / Commit ]
 (Arquivos editados)                     (Arquivos prontos)                     (Histórico salvo)
```

### 🛠️ Comandos Essenciais do Git para Prova

#### Configuração Inicial e Inspeção
- `git config --global user.name "Seu Nome"`: Configura o nome do autor.
- `git config --global user.email "seu@email.com"`: Configura o e-mail do autor.
- `git init`: Inicializa um repositório Git local (cria a pasta oculta `.git`).
- `git status`: Exibe o estado dos arquivos (modificados, adicionados no staging, não rastreados).
- `git log` / `git log --oneline`: Exibe o histórico de commits (versão compacta).
- `git diff`: Mostra as alterações no *Working Directory* ainda não adicionadas ao staging.
- `git diff --staged`: Mostra as alterações que estão no *Staging Area* prontas para o commit.

#### Adicionando e Registrando Alterações
- `git add .`: Adiciona todas as alterações do diretório atual ao Staging Area.
- `git add arquivo.txt`: Adiciona um arquivo específico ao Staging.
- `git commit -m "Mensagem descritiva"`: Cria um commit com as alterações do Staging.

#### Desfazendo Alterações e Manipulando Histórico
- `git restore arquivo.txt`: Descarta as alterações do arquivo no Working Directory.
- `git restore --staged arquivo.txt`: Remove o arquivo do Staging sem perder as edições.
- `git revert HASH`: Desfaz as alterações de um commit **criando um novo commit de cancelamento**, sem alterar/reescrever o histórico.

#### 🎛️ Tabela Comparativa dos Modos do `git reset` (Reescrevendo o Histórico)
O comando `git reset` move o ponteiro `HEAD` para outro commit do histórico:

| Comando | Ponteiro HEAD | Staging Area | Working Directory (Arquivos) |
| :--- | :--- | :--- | :--- |
| `git reset --soft HASH` | **Move** | **Mantém** as alterações | **Mantém** as alterações intactas |
| `git reset --mixed HASH` *(padrão)* | **Move** | **Reseta** (limpa o staging) | **Mantém** as alterações nos arquivos |
| `git reset --hard HASH` | **Move** | **Reseta** (limpa o staging) | **Reseta** (DELETA alterações nos arquivos) |

---

### 🔀 Branches, Merges e Pull Requests (PR)

- **Branch**: Uma linha de desenvolvimento independente criada a partir da branch principal (`main`/`master`).
  - `git branch nome-branch`: Cria uma nova branch.
  - `git switch nome-branch` ou `git checkout nome-branch`: Muda para a branch especificada.
  - `git switch -c nome-branch` ou `git checkout -b nome-branch`: Cria e muda para a branch em um único comando.
- **Merge**: Processo de mesclar alterações de uma branch em outra (ex: integrar `feature` na `main`).
  - `git switch main` ➔ `git merge feature-login`
- **Pull Request (PR)**: **Mecanismo de colaboração do GitHub** (não é comando Git!). É a solicitação formal de um desenvolvedor para que suas alterações de uma branch (`compare`) sejam revisadas e integradas na branch principal (`base`).

---

## ⚙️ 3. Estrutura e Sintaxe do GitHub Actions (Aula 03)

### 📄 O que é um Workflow?
Um **Workflow** é um processo automatizado configurado no GitHub através de arquivos em formato **YAML** salvos obrigatoriamente no caminho:
`.github/workflows/*.yml`

---

### 🧩 Anatomia da Sintaxe YAML no GitHub Actions

```yaml
name: Nome Visivel da Pipeline # Nome exibido na aba Actions do GitHub

on: # Gatilhos / Eventos que disparam o workflow
  push:
    branches: [main, dev] # Dispara no push para main ou dev
  pull_request:
    branches: [main]

jobs: # Bloco de tarefas (jobs)
  teste: # ID do job
    runs-on: ubuntu-latest # Máquina virtual temporária (Runner)
    
    steps: # Passos sequenciais executados dentro do job
      - name: Baixar Código
        uses: actions/checkout@v4 # Action pronta do Marketplace

      - name: Configurar Python
        uses: actions/setup-python@v5
        with: # Parâmetros extras para a Action acima
          python-version: '3.12'

      - name: Executar Testes
        working-directory: ativ-01 # Diretório onde o comando será rodado
        run: pytest # Comando de terminal executado no runner
```

---

### 🎯 Diferença Fundamental entre `uses`, `with` e `run`

| Palavra-Chave | Função | Analogia em Frase | Exemplo |
| :--- | :--- | :--- | :--- |
| **`uses`** | Executa uma Action reutilizável pré-existente do Marketplace. | *"Use esta ferramenta pronta"* | `uses: actions/checkout@v4` |
| **`with`** | Passa parâmetros de configuração extras para a Action do `uses`. | *"Configure essa ferramenta assim"* | `with: python-version: '3.12'` |
| **`run`** | Executa comandos ou scripts diretos no terminal do Runner. | *"Execute este comando no shell"* | `run: pytest` |

---

### 🖥️ O que é `runs-on` e o Isolamento de Jobs

- **`runs-on`**: Define o sistema operacional e ambiente da máquina virtual (Runner) criada temporariamente pelo GitHub.
  - **Runners Oficiais do GitHub**: `ubuntu-latest`, `ubuntu-24.04`, `ubuntu-22.04`, `windows-latest`, `macos-latest`.
  - **Self-Hosted Runner**: Servidor próprio da empresa configurado em `Settings > Actions > Runners` (`runs-on: self-hosted`).
- **Isolamento de Jobs**:
  - Cada `job` roda em uma **máquina limpa e totalmente isolada**.
  - Jobs **não compartilham arquivos** entre si por padrão (compartilham apenas o status de sucesso/falha).
  - Por padrão, jobs **sem dependência rodam em PARALELO**.
  - Para criar dependência sequencial, usa-se a chave **`needs: [nome-do-job-anterior]`**.

---

## 🧪 4. Qualidade de Software, Shift Left e Pirâmide de Testes (Aula 04)

### ⬅️ O Princípio *Shift Left* e o Custo Exponencial do Erro
- **Shift Left (Mover para a Esquerda)**: Prática de trazer as validações de qualidade e segurança para o **início do ciclo de desenvolvimento** (fase de commit/CI), em vez de testar apenas na véspera do deploy.
- **Custo Exponencial do Erro**: Corrigir um bug durante o commit custa centavos; corrigir o mesmo bug após o código chegar em produção gera prejuízo financeiro, retrabalho e dano à reputação.
- **Funil de Feedback**: O pipeline atua como um funil automatizado onde verificações rápidas barram código quebrado em poucos segundos.

---

### 🔺 A Pirâmide de Testes

```text
       /\
      /  \     E2E (End-to-End) -> Poucos testes, lentos, simulam jornada do usuário
     /----\
    / Inte- \  Integração -> Testam comunicação entre módulos e banco de dados
   /  gração \
  /------------\
 /  Unitários   \ Unitários -> Base da pirâmide. Milhares rodando em segundos, isolados
/________________\
```

1. **Testes Unitários (Base)**: Testam a menor unidade isolada de código (uma função ou método) em memória. São ultra-rápidos e baratos.
2. **Testes de Integração (Meio)**: Testam a comunicação entre diferentes componentes da aplicação (ex: API + Banco de Dados).
3. **Testes E2E / End-to-End (Topo)**: Simulam o fluxo real do usuário do início ao fim abrindo navegadores e clicando nas telas. São lentos e pesados.

---

### 📐 Anatomia de um Teste Automatizado: Padrão AAA (Arrange, Act, Assert)

- **Arrange (Organizar)**: Prepara os dados de entrada, mocks e o cenário do teste.
- **Act (Agir)**: Executa a função ou método que está sendo testado.
- **Assert (Verificar)**: Compara o resultado obtido com o resultado esperado.

```python
def test_soma():
    # 1. Arrange (Organizar)
    numero1 = 2
    numero2 = 3
    
    # 2. Act (Agir)
    resultado = soma(numero1, numero2)
    
    # 3. Assert (Verificar)
    assert resultado == 5
```

---

### 📊 Code Coverage (Cobertura de Testes)
- **Conceito**: Métrica percentual (%) que indica quanto do código-fonte foi executado durante a bateria de testes.
- **Papel de Quality Gate**: Se a cobertura configurada (ex: 80%) não for atingida, o build do pipeline falha automaticamente.
- ⚠️ **ATENÇÃO PARA A PROVA**: Cobertura alta garante que as linhas de código foram executadas, mas **não garante por si só que os testes são semanticamente bons**.

---

## 🔀 5. Estratégia de Matrix, Cache, Secrets, Variables e DevSecOps (Aula 05)

### 🔀 1. Estratégia de Matrix (`strategy: matrix`)
Permite executar um mesmo job em múltiplos ambientes (sistemas operacionais, versões de linguagens) sem duplicar código no YAML:

```yaml
strategy:
  matrix:
    os: [ubuntu-latest, windows-latest] # 2 SOs
    python-version: ['3.10', '3.11']    # 2 versões do Python
# Total = 2 SOs x 2 Pythons = 4 execuções paralelas!
```

- **Regra "Não matar barata com canhão"**: Evitar matrizes gigantescas desnecessárias para não esgotar a cota de minutos pagos do GitHub Actions.

---

### ⚡ 2. Caching de Dependências (`cache: 'pip'`)
- **Problema**: A cada execução da pipeline, o runner limpo precisa rebaixar todas as bibliotecas da internet via `pip` ou `npm`.
- **Solução**: Armazenar temporariamente em nuvem o diretório de dependências baixadas.
- **Funcionamento**: A action `actions/setup-python@v5` calcula o **hash do `requirements.txt`**. Se o arquivo não mudou, os pacotes são restaurados do Cache em **2 a 3 segundos**.

```yaml
- name: Configurar Python
  uses: actions/setup-python@v5
  with:
    python-version: ${{ matrix.python-version }}
    cache: 'pip'
    cache-dependency-path: '**/requirements.txt'
```

---

### 🔐 3. Secrets vs. Variables no GitHub

- **Variables (`${{ vars.NOME }}`)**: Configurações **não sensíveis** e públicas (`Settings > Secrets and variables > Actions > Variables`).
- **Secrets (`${{ secrets.NOME }}`)**: Informações **sensíveis e criptografadas** (tokens, senhas, chaves de API, `SONAR_TOKEN`).
  - **Proteção nos Logs**: O GitHub mascara automaticamente qualquer tentativa de exibição de um Secret substituindo o valor por `***`.

---

### 🛡️ 4. Recursos Nativos de Segurança do GitHub (Aba "Security and quality")

1. **Dependabot**: Notifica vulnerabilidades conhecidas em bibliotecas e abre Pull Requests automáticos de correção.
2. **Code Scanning (CodeQL / SAST)**: Varre o código procurando por falhas graves de segurança (SQL Injection, XSS, erros de lógica).
3. **Secret Scanning & Push Protection**: Bloqueia o `git push` **antes** que o commit seja aceito no servidor se detectar chaves de API ou senhas válidas.

---

## 🛑 6. Entendendo do Básico & Síntese da Ordem de Execução

### 🛑 O Básico com Analogia da Cozinha
- **Sequencial**: Imagine preparar uma pizza. Você precisa abrir a massa, colocar o recheio e só então assar. A ordem é obrigatória e um passo depende do anterior.
- **Paralelo**: Imagine uma equipe na cozinha. Enquanto um cozinheiro pica os legumes, outro prepara a sobremesa. Ambos trabalham ao mesmo tempo para economizar tempo total.

### 🎯 Síntese: Por que a Ordem da Pipeline é Escolhida Assim?

1. **Filtragem Rápida e Barata (Fail-Fast)**: Linter e Gitleaks rodam primeiro (em paralelo) porque levam poucos segundos. Se houver um erro bobo de sintaxe ou vazamento de senha, a pipeline falha em 3 segundos sem gastar máquinas com testes mais pesados.
2. **Dependência Lógica**: Testes unitários (rápidos e em memória) rodam antes dos testes de Integração/E2E (lentos e pesados). Não faz sentido testar telas e navegadores se a lógica interna do código falhou.
3. **Segurança no Deploy**: O *Deploy* é a última etapa sequencial (`needs: [todos-os-jobs]`), garantindo que apenas código 100% testado e seguro chegue aos usuários.

---

## 📦 7. Gerenciamento de Dependências com `requirements.txt`

### 📄 O que é o `requirements.txt`?
O `requirements.txt` é um arquivo de texto simples usado no ecossistema Python que lista todas as bibliotecas e pacotes externos (com suas respectivas versões) que a sua aplicação necessita para ser executada.

### 🎯 Por que ele é fundamental em CI/CD?
1. **Reprodutibilidade do Ambiente**: Garante que o projeto rode **exatamente com as mesmas versões de bibliotecas** em qualquer computador ou máquina virtual do GitHub Actions.
2. **Integração com o Cache no GitHub Actions**:
   - O `actions/setup-python@v5` calcula um **hash** (identificador único) do conteúdo do `requirements.txt`.
   - Se o arquivo não mudou entre as execuções, o GitHub Actions restaura os pacotes direto do Cache em segundos.
   - Se você adicionar um novo pacote no `requirements.txt`, o hash muda automaticamente, fazendo o CI/CD baixar a nova dependência e atualizar o Cache.

---

## ☁️ 8. Passo a Passo Atualizado do SonarCloud no GitHub Actions

O **SonarCloud** é uma plataforma em nuvem para **Análise Estática de Código (SAST)** que avalia **Bugs, Vulnerabilidades, Code Smells, Duplicações** e aplica o **Quality Gate** (Portão de Qualidade).

```text
[ Commit / Pull Request ] ──► [ GitHub Actions executa os testes ]
                                              │
                                              ▼
[ Quality Gate: Aprovado/Reprovado ] ◄── [ SonarCloud analisa o código ]
```

### 📋 Passo a Passo Oficial e Atualizado de Configuração

#### Passo 1: Importar o Repositório no SonarCloud
1. Acesse [sonarcloud.io](https://sonarcloud.io) e faça login com sua conta do GitHub.
2. Clique em **+ (Plus)** no canto superior direito ➔ **Analyze new project**.
3. Selecione sua Organização do GitHub e escolha o repositório do projeto.

#### Passo 2: Gerar o Token de Segurança no SonarCloud
1. No SonarCloud, clique na foto do seu perfil ➔ **My Account**.
2. Acesse a aba **Security**.
3. No campo **Generate Token**, digite um nome (ex: `GITHUB_ACTIONS_TOKEN`) e clique em **Generate**. Copie o token gerado.

#### Passo 3: Cadastrar o Segredo no GitHub
1. No seu repositório do GitHub, acesse **Settings ➔ Secrets and variables ➔ Actions**.
2. Clique no botão **New repository secret**.
3. **Name**: `SONAR_TOKEN`
4. **Secret**: Cole o token copiado do SonarCloud e clique em **Add secret**.

#### Passo 4: Criar o Arquivo `sonar-project.properties` na Raiz do Repositório
Crie um arquivo chamado `sonar-project.properties` na raiz do seu projeto informando as chaves obtidas no painel do SonarCloud:

```properties
sonar.organization=sua-organizacao-sonar
sonar.projectKey=sua-organizacao_seu-repositorio
sonar.sources=.
sonar.exclusions=**/node_modules/**,**/tests/**,**/coverage/**
sonar.tests=.
sonar.test.inclusions=**/*.test.js,**/test_*.py
sonar.python.version=3.12
```

#### Passo 5: Criar o Workflow do SonarCloud no GitHub Actions (`.github/workflows/sonarcloud.yml`)

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
      - name: Baixar Código com Histórico Completo
        uses: actions/checkout@v4
        with:
          fetch-depth: 0 # OBRIGATÓRIO: O SonarCloud precisa do histórico do Git para atribuir autoria das linhas

      - name: Executar SonarCloud Scanner
        uses: SonarSource/sonarcloud-github-action@master
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }} # Token automático do GitHub para ler PRs
          SONAR_TOKEN: ${{ secrets.SONAR_TOKEN }}   # Token cadastrado nos Secrets do repositório
```

---

Com este guia unificado, você tem **100% do conteúdo das aulas 01 a 05** e todas as seções trazidas até aqui organizadas sem nenhuma perda de informação!
