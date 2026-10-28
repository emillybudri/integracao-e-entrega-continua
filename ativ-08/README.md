# Mural de mensagens — aula de CI/CD

Aplicação web pequena para trabalhar Docker Compose e GitHub Actions:
- HTML para a interface;
- Flask para receber/exibir mensagens;
- MariaDB para armazenar as mensagens.

Não inclui healthcheck nem observabilidade nesta etapa da aula.

## Estrutura
- `index.html` está em `templates/index.html`.
- `app.py` contém as rotas Flask e a conexão com MariaDB.
- `Dockerfile` constrói a imagem da aplicação.
- `docker-compose.yml` sobe `app` e `db`.
- `.github/workflows/pipeline.yml` implementa Build + Publish → Test → Deploy.

## Preparação

1. Crie um repositório no GitHub e envie os arquivos para a branch `main`.
2. Crie no Docker Hub um repositório chamado `mural-flask` (usuário: `emillybudri`).
3. Em `docker-compose.yml`, a imagem está configurada como `emillybudri/mural-flask:latest`.
4. Em GitHub → Settings → Secrets and variables → Actions, crie:
   - `DOCKERHUB_USERNAME` (`emillybudri`)
   - `DOCKERHUB_TOKEN`
   - `TEST_DB_PASSWORD` e `TEST_DB_ROOT_PASSWORD` (credenciais usadas no runner de testes)
   - `PROD_DB_PASSWORD` e `PROD_DB_ROOT_PASSWORD` (credenciais de produção, usadas para criar o `.env` na VM)
   - `SSH_HOST`
   - `SSH_USER`
   - `SSH_KEY`
5. Na VM Azure, instale Docker e Docker Compose.
6. A pipeline cria/atualiza o `.env` na VM durante o deploy. Não crie nem versione esse arquivo manualmente.
7. Para rodar localmente, crie um `.env` com `DB_PASSWORD` e `DB_ROOT_PASSWORD` ou defina as variáveis no ambiente, depois execute `docker compose up --build -d`.
8. Acesse `http://localhost:6767`.

## Fluxo da pipeline
1. **Build + Publish:** valida o Compose, constrói a imagem (`emillybudri/mural-flask:latest`) e publica `latest` no Docker Hub.
2. **Test:** baixa a imagem publicada, inicia Flask + MariaDB e verifica que a página principal responde via HTTP na porta `6767`.
3. **Deploy:** se os testes passarem, atualiza o repositório na VM, cria o `.env` com os Secrets de produção, baixa as imagens e atualiza os containers na porta `6767`.

## Secrets
- `DOCKERHUB_USERNAME`, `DOCKERHUB_TOKEN`: publicação/obtenção da imagem no runner.
- `TEST_DB_PASSWORD`, `TEST_DB_ROOT_PASSWORD`: credenciais de teste.
- `PROD_DB_PASSWORD`, `PROD_DB_ROOT_PASSWORD`: credenciais de produção enviadas à VM.
- `SSH_HOST`, `SSH_USER`, `SSH_KEY`: acesso SSH.

## Observações
- A imagem é publicada antes dos testes, conforme a proposta didática. Se o teste falhar, o deploy é bloqueado, mas a tag `latest` já terá sido atualizada.
- `docker compose down -v` remove o volume do banco apenas no runner temporário de testes.
- Para simplificar, deixe o repositório Docker Hub público. Se for privado, a VM também precisa fazer login no Docker Hub.
- O script de `.env` foi simplificado para senhas sem aspas simples nem quebras de linha. Em produção, prefira um gerenciamento de segredos mais robusto.

