# Atividade 05 (02/09/2026) - Pipeline CI com Estratégia de Matrix

Esta atividade modifica o pipeline de CI para utilizar a estratégia de **Matrix** no GitHub Actions, executando os testes em 6 combinações paralelas.

---

## 📌 Requisitos da Atividade

1. **Sistemas Operacionais (SO)**: 3 versões válidas de Ubuntu no GitHub Actions:
   - `ubuntu-latest`
   - `ubuntu-24.04`
   - `ubuntu-22.04`
2. **Versões do Python**:
   - `3.10`
   - `3.11`

---

## ⚙️ Estrutura da Estratégia de Matrix

A matriz resulta em **6 combinações de jobs paralelos** (3 SOs × 2 Versões de Python = 6 testes):

```yaml
jobs:
  qualidade:
    name: Testes (${{ matrix.os }} - Python ${{ matrix.python-version }})
    runs-on: ${{ matrix.os }}
    strategy:
      matrix:
        os: [ubuntu-latest, ubuntu-24.04, ubuntu-22.04]
        python-version: ['3.10', '3.11']
```

---

## ⚡ Otimização com Caching (`cache: 'pip'`)

Para evitar rebaixar todas as dependências a cada execução da matriz, o parâmetro `cache: 'pip'` foi adicionado à action `actions/setup-python@v5`. Isso garante que as dependências sejam salvas na primeira execução e restauradas em segundos nas seguintes.

---

## 🗂️ Arquivos da Atividade

- **Workflow**: [`.github/workflows/ativ-05-pipeline-matrix-py.yml`](../.github/workflows/ativ-05-pipeline-matrix-py.yml)
- **Testes**: [`test_matrix.py`](test_matrix.py)
