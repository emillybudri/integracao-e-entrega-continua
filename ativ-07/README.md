# 📝 Atividade 07 - Ambiente WordPress + MariaDB com Docker

Esta atividade demonstra a criação e orquestração de um ambiente completo de **Gerenciamento de Conteúdo (CMS - WordPress)** integrado a um **Banco de Dados Relacional (MariaDB)** utilizando Docker e Docker Compose.

---

## 🏗️ Arquitetura da Aplicação

A aplicação é composta por 2 serviços em containers interligados por uma rede virtual dedicada:

| Serviço | Imagem Oficial | Função | Porta / Acesso |
| :--- | :--- | :--- | :--- |
| **Serviço 1 (Web)** | `wordpress:latest` | Aplicação Web WordPress (Apache/PHP) | Port 8081 (`http://localhost:8081`) |
| **Serviço 2 (Database)** | `mariadb:latest` | Banco de dados relacional para persistência de dados | Porta 3306 (Interna da rede Docker) |

---

## 🚀 Como Executar o Ambiente

Existem duas formas de subir esta infraestrutura: usando **Docker Compose** (recomendado) ou via **Comandos Docker CLI manuais**.

---

### Método 1: Utilizando Docker Compose (Recomendado)

O arquivo [`docker-compose.yml`](docker-compose.yml) já gerencia as dependências, variáveis de ambiente, volumes persistentes e a rede dos containers.

#### 1. (Opcional) Configurar variáveis de ambiente
Crie um arquivo `.env` baseado no `.env.example`:
```bash
cp .env.example .env
```

#### 2. Subir a aplicação em segundo plano (detached mode)
No diretório `ativ-07/`, execute:
```bash
docker compose up -d
```

#### 3. Verificar o status dos containers
```bash
docker compose ps
```

#### 4. Acessar o WordPress
Abra o navegador e acesse: [http://localhost:8081](http://localhost:8081)

#### 5. Parar os containers
```bash
# Parar containers mantendo os dados nos volumes
docker compose down

# Parar containers e remover volumes (reset completo)
docker compose down -v
```

---

### Método 2: Executando via Comandos Docker CLI Manuais

Caso precise subir os containers individualmente sem usar Docker Compose:

#### 1. Criar a rede compartilhada
```bash
docker network create wordpress_net
```

#### 2. Criar os volumes persistentes
```bash
docker volume create db_data
docker volume create wordpress_data
```

#### 3. Subir o container do MariaDB (Database)
```bash
docker run -d \
  --name mariadb_wordpress \
  --network wordpress_net \
  -e MYSQL_ROOT_PASSWORD=root_secret_password \
  -e MYSQL_DATABASE=wordpress_db \
  -e MYSQL_USER=wp_user \
  -e MYSQL_PASSWORD=wp_password \
  -v db_data:/var/lib/mysql \
  mariadb:latest
```

#### 4. Subir o container do WordPress (Web)
```bash
docker run -d \
  --name wordpress_web \
  --network wordpress_net \
  -p 8081:80 \
  -e WORDPRESS_DB_HOST=mariadb_wordpress:3306 \
  -e WORDPRESS_DB_NAME=wordpress_db \
  -e WORDPRESS_DB_USER=wp_user \
  -e WORDPRESS_DB_PASSWORD=wp_password \
  -v wordpress_data:/var/www/html \
  wordpress:latest
```

#### 5. Limpeza dos containers manuais
```bash
docker stop wordpress_web mariadb_wordpress
docker rm wordpress_web mariadb_wordpress
docker network rm wordpress_net
```

---

## 🛠️ Variáveis de Ambiente Utilizadas

| Variável | Descrição |
| :--- | :--- |
| `MYSQL_ROOT_PASSWORD` | Senha de superusuário (root) do MariaDB |
| `MYSQL_DATABASE` | Nome do banco de dados criado automaticamente para o WordPress (`wordpress_db`) |
| `MYSQL_USER` | Usuário da aplicação no MariaDB (`wp_user`) |
| `MYSQL_PASSWORD` | Senha do usuário da aplicação (`wp_password`) |
| `WORDPRESS_DB_HOST` | Endereço e porta do serviço de banco de dados (`database:3306`) |
| `WORDPRESS_DB_NAME` | Nome do banco que o WordPress deve conectar |
| `WORDPRESS_DB_USER` | Usuário de autenticação do WordPress no banco |
| `WORDPRESS_DB_PASSWORD` | Senha do WordPress para conexão com o MariaDB |

---

## 🛡️ Destaques da Implementação

- **Healthcheck e Dependência**: O container `web` aguarda o MariaDB estar completamente saudável (`service_healthy`) antes de iniciar.
- **Persistência de Dados**: Volumes nomeados (`db_data` e `wordpress_data`) garantem que postagens, configurações e mídias do WordPress não sejam perdidos ao reiniciar os containers.
- **Isolamento de Rede**: Comunicação segura entre a aplicação web e o banco de dados via `bridge network`.
