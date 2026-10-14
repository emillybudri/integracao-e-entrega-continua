# Atividade 06 (30/09/2026) - Site Clone Pudim (Dockerfile Edition) & Containerização

Esta atividade consiste na criação de um site exatamente no mesmo formato e layout minimalista do site clássico **`pudim.com.br`**, onde a imagem original do pudim foi substituída pela representação de um **`Dockerfile`**. Além disso, o projeto possui a sua própria estrutura de containerização com Docker, instruções de envio para Container Registry (Docker Hub) e pipeline de CI/CD automatizada.

---

## 📌 Requisitos da Atividade

1. **Interface Visual Exact Replica**:
   - Layout centralizado com container branco sobre fundo cinza `#d6d6d6`.
   - Substituição da foto do pudim pela imagem/código de um `Dockerfile`.
   - Link inferior original para `pudim@pudim.com.br`.
2. **Containerização Docker**:
   - Criação do [`Dockerfile`](Dockerfile) baseado na imagem oficial leve `nginx:alpine`.
   - Publicação e exposição na porta `80`.
3. **Publicação no Container Registry (Docker Hub)**:
   - Login, taggeamento da imagem e envio via `docker push`.
4. **Pipeline de CI no GitHub Actions**:
   - Workflow de validação da estrutura HTML/Dockerfile e execução de build/push do container Docker.

---

## ⚙️ Estrutura do Projeto

```text
ativ-06/
├── Dockerfile           # Configuração de build e publicação via NGINX Alpine
├── docker-compose.yml   # Orquestração para executar a imagem do Docker Hub
├── .dockerignore        # Arquivos ignorados na imagem Docker
├── dockerfile.png       # Imagem da sintaxe Dockerfile em destaque
├── index.html           # Página HTML clone idêntica ao pudim.com.br
└── README.md            # Documentação da atividade
```

---

## 🐳 Como Executar com Docker Localmente

### Opção 1: Usando Docker Compose com a Imagem do Docker Hub (Recomendado)

Em vez de compilar o projeto localmente, utilize a imagem publicada no Docker Hub (`emillybudri/pudim-dockerfile:latest`) configurada no `docker-compose.yml`:

```yaml
services:
  app:
    image: emillybudri/pudim-dockerfile:latest
    container_name: site-pudim-app
    ports:
      - "6767:80"
    restart: always
```

Execute o comando:
```bash
docker compose up -d
```

Acesse no navegador:
`http://localhost:6767`

Para parar o container:
```bash
docker compose down
```

---

### Opção 2: Compilar e Executar Manualmente via Docker

```bash
# 1. Construir a imagem Docker
docker build -t pudim-dockerfile:latest .

# 2. Rodar o container na porta 6767
docker run -d -p 6767:80 --name site-pudim-app pudim-dockerfile:latest

# 3. Acesse no navegador:
# http://localhost:6767
```

---

## 🚀 Publicação no Container Registry (Docker Hub)

Assim como o código-fonte vai para o GitHub, a imagem compilada vai para um **Container Registry** (geralmente o **Docker Hub**).

### Passo a Passo no Terminal:

1. **Autenticação no Docker Hub com Personal Access Token**:
   ```bash
   docker login -u emillybudri
   # Ao solicitar a senha, insira o seu Personal Access Token (ex: dckr_pat_...)
   ```

2. **Carimbar / Taggear a imagem com o seu usuário do Docker Hub**:
   ```bash
   docker tag pudim-dockerfile:latest emillybudri/pudim-dockerfile:latest
   ```

3. **Publicar a Imagem no Registry (`docker push`)**:
   ```bash
   docker push emillybudri/pudim-dockerfile:latest
   ```

---

## ☁️ Deploy Contínuo (CD) no Servidor de Produção (Azure VM via SSH)

Seguindo a arquitetura de **Continuous Delivery** apresentada em aula (Slide 11 e 13):

1. **Servidor Compartilhado na Azure**:
   - `app-docker-aula` (aplicação do professor) roda na porta `80:80`.
   - `site-pudim-app` (aplicação da Emilly) roda na porta `6767:80`.
   - Ambos os containers coexistem de forma isolada na mesma VM.

2. **Mecanismo de Transporte SSH**:
   - O GitHub Actions conecta via SSH utilizando a action `appleboy/ssh-action@v1`.
   - Utiliza os segredos criptografados:
     - `AZURE_VM_HOST`: Endereço IP público da VM.
     - `AZURE_VM_USER`: Usuário administrador da VM.
     - `AZURE_VM_SSH_KEY`: Chave privada SSH.

3. **Comando de Deploy Executado no Servidor**:
   ```bash
   docker pull emillybudri/pudim-dockerfile:latest
   docker rm -f site-pudim-app || true
   docker run -d --name site-pudim-app -p 6767:80 --restart unless-stopped emillybudri/pudim-dockerfile:latest
   ```

4. **Estágio de Aprovação (Environment Protection)**:
   - Configurado com `environment: producao`.
   - Exige aprovação manual (*Required Reviewers*) antes de disparar o deploy em produção, cumprindo o critério de Entrega Contínua (CD).

- **URL da Aplicação no Servidor**: `http://<AZURE_VM_HOST>:6767`
- **URL Alternativa (Render)**: [https://pudim-dockerfile-v1.onrender.com/](https://pudim-dockerfile-v1.onrender.com/)

---

## 🗂️ Arquivos Relacionados

- **Workflow de CI/CD**: [`.github/workflows/ativ-06-pipeline-docker-pudim.yml`](../.github/workflows/ativ-06-pipeline-docker-pudim.yml)
- **Docker Compose**: [`docker-compose.yml`](docker-compose.yml)
- **Código HTML com identificação do aluno**: [`index.html`](index.html)
- **Dockerfile**: [`Dockerfile`](Dockerfile)


