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
