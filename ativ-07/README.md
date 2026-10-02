# 🌐 Atividade 07: WordPress + MariaDB com Docker

Ambiente completo de **CMS (WordPress)** com **banco relacional (MariaDB)**, orquestrado com Docker Compose. Ele sobe **já instalado**: sem assistente de instalação, com o tema ativo, publicações iniciais e um usuário pronto para entrar.

<p align="center">
  <img src="screenshots/home.png" alt="Página inicial do Portal CI/CD" width="520" />
</p>

---

## ⚡ Acesso rápido

```bash
docker compose up -d --build
```

1. Abra **http://localhost:8081** (leva alguns segundos na primeira vez, enquanto o WordPress se instala sozinho).
2. Clique em **Entrar**. A tela de login mostra o usuário e a senha.
3. Entre com **`admin`** / **`admin123`**. Você volta para o site, onde aparece o botão **+** para criar publicações. Cada publicação ganha *Editar* e *Excluir*.

> [!NOTE]
> Usuário e senha são só para uso local. Para trocá-los, copie `.env.example` para `.env` e edite `PORTAL_ADMIN_USER` e `PORTAL_ADMIN_PASSWORD` **antes** do primeiro `up`.

---

## 🏗️ Arquitetura

| Serviço | Imagem | Função | Acesso |
| :--- | :--- | :--- | :--- |
| `web` | build local sobre `wordpress:7.1.2-php8.3-apache` | WordPress com tema, WP-CLI e instalação automática | `http://localhost:8081` |
| `database` | `mariadb:10.11.19` | Banco de dados relacional | Porta 3306, só dentro da rede |

Os dois ficam na rede `bridge` dedicada. O `web` só inicia depois que o banco passa no healthcheck (`depends_on` com `service_healthy`). Os dados vivem nos volumes `db_data` e `wordpress_data`.

### Como a instalação automática funciona

1. O [`Dockerfile`](Dockerfile) parte da imagem oficial do WordPress e acrescenta o WP-CLI, o tema, os `mu-plugins` e o conteúdo inicial.
2. O [`entrypoint.sh`](docker/entrypoint.sh) inicia o provisionamento em segundo plano e deixa o entrypoint oficial seguir normalmente.
3. O [`provision.sh`](docker/provision.sh) espera o banco responder, e **só se o site ainda não estiver instalado** executa `wp core install`, ativa o idioma pt_BR e o tema, remove o conteúdo de exemplo e cria as publicações de [`seed/seed.php`](seed/seed.php).
4. Em um volume que já tem o site instalado, nada é alterado.

---

## 🔧 Variáveis

Todas são opcionais. Sem `.env`, valem os padrões do [`.env.example`](.env.example).

| Variável | Padrão | Descrição |
| :--- | :--- | :--- |
| `WP_PORT` | `8081` | Porta do site no seu computador |
| `PORTAL_ADMIN_USER` | `admin` | Usuário administrador criado na instalação |
| `PORTAL_ADMIN_PASSWORD` | `admin123` | Senha do administrador. Se vazia, uma senha aleatória é gerada e mostrada nos logs |
| `PORTAL_ADMIN_EMAIL` | `admin@example.com` | E-mail do administrador |
| `PORTAL_SEED_POSTS` | `1` | `1` cria as publicações iniciais, `0` deixa o site vazio |
| `PORTAL_LOGIN_HINT` | `1` | `1` mostra usuário e senha na tela de login |
| `MYSQL_ROOT_PASSWORD`, `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD` | ver `.env.example` | Credenciais do MariaDB, repassadas ao WordPress |

---

## 🧰 Comandos úteis

```bash
docker compose ps                 # estado dos containers
docker compose logs -f web        # acompanha a instalação automática ([portal] ...)
docker compose down               # para, mantendo o banco e as publicações
docker compose down -v            # para e APAGA os volumes (reinstala do zero no próximo up)
```

---

## ☁️ Publicar no Render

O Render executa **um container por serviço** e não lê `docker-compose.yml`, então o banco precisa ser externo ao serviço web.

1. Crie um banco **MariaDB ou MySQL** em um provedor externo e anote host, porta, nome, usuário e senha.
2. No Render, crie um **Web Service** a partir deste repositório, com **Root Directory** `ativ-07` e ambiente **Docker**.
3. Defina as variáveis do serviço:

   | Variável | Valor |
   | :--- | :--- |
   | `WORDPRESS_DB_HOST` | `host:porta` do banco |
   | `WORDPRESS_DB_NAME`, `WORDPRESS_DB_USER`, `WORDPRESS_DB_PASSWORD` | credenciais do banco |
   | `PORTAL_ADMIN_USER`, `PORTAL_ADMIN_PASSWORD` | **defina uma senha forte**; não use `PORTAL_LOGIN_HINT` em produção |
   | `WORDPRESS_CONFIG_EXTRA` | `define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);` (somente se o banco exigir SSL) |

4. No primeiro deploy, o site se instala sozinho usando o endereço do próprio Render (`RENDER_EXTERNAL_URL`), e já abre com o tema e as publicações.

> [!WARNING]
> No plano gratuito do Render o serviço **hiberna** após alguns minutos sem acesso, e a primeira visita seguinte pode levar cerca de um minuto para responder. Isso é do plano, não do site.

---

## 🐳 Alternativa: Docker CLI, sem Compose

```bash
docker network create wordpress_net
docker volume create db_data
docker volume create wordpress_data

docker run -d --name mariadb_wordpress --network wordpress_net \
  -e MYSQL_ROOT_PASSWORD=root_secret -e MYSQL_DATABASE=wordpress_db \
  -e MYSQL_USER=wp_user -e MYSQL_PASSWORD=wp_password \
  -v db_data:/var/lib/mysql mariadb:10.11.19

docker build -t portal-cicd-wordpress .

docker run -d --name wordpress_web --network wordpress_net -p 8081:80 \
  -e WORDPRESS_DB_HOST=mariadb_wordpress:3306 -e WORDPRESS_DB_NAME=wordpress_db \
  -e WORDPRESS_DB_USER=wp_user -e WORDPRESS_DB_PASSWORD=wp_password \
  -e PORTAL_SITE_URL=http://localhost:8081 \
  -e PORTAL_ADMIN_PASSWORD=admin123 -e PORTAL_LOGIN_HINT=1 \
  -v wordpress_data:/var/www/html portal-cicd-wordpress
```

Sem o Compose não há healthcheck nem `depends_on`: o provisionamento já espera o banco responder por conta própria. Para limpar:

```bash
docker stop wordpress_web mariadb_wordpress && docker rm wordpress_web mariadb_wordpress
docker network rm wordpress_net && docker volume rm db_data wordpress_data
```

---

## 📁 Arquivos

```text
ativ-07/
├── Dockerfile                # WordPress + WP-CLI + tema + conteúdo inicial
├── docker-compose.yml        # web + database, rede, volumes e healthcheck
├── .env.example              # Variáveis opcionais
├── docker/                   # entrypoint.sh e provision.sh (instalação automática)
├── seed/                     # Publicações iniciais e suas ilustrações
├── mu-plugins/               # Ativa o tema, ajusta a URL e a tela de login
├── theme/                    # Tema "Portal CI/CD" (PHP, CSS e JS do CRUD)
└── screenshots/              # Imagens usadas na documentação
```

## 🛡️ Destaques

- **Primeiro acesso sem atrito:** instalação, idioma, tema e conteúdo prontos, sem passar pelo assistente.
- **Idempotente:** reiniciar ou refazer o `up` não reinstala nem duplica nada.
- **Funciona em qualquer endereço:** `localhost`, `127.0.0.1` ou o domínio do Render, sem reconfigurar a URL.
- **Healthcheck e dependência:** o WordPress só inicia com o banco saudável.
- **Persistência:** postagens, configurações e mídias sobrevivem a reinicializações.
- **Pipeline:** o [workflow](../.github/workflows/ativ-07-pipeline-docker-wordpress.yml) valida o Compose, constrói a imagem, espera a instalação automática e confere o site, as publicações e a tela de login.
