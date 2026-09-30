# Imagem base leve com servidor web NGINX
FROM nginx:alpine

# Metadata da imagem
LABEL maintainer="Emilly Budri Bognar"
LABEL description="Atividade 06 - Clone do pudim.com.br com imagem de Dockerfile"

# Copia os arquivos estáticos para o diretório de publicação do NGINX
COPY index.html /usr/share/nginx/html/index.html
COPY dockerfile.png /usr/share/nginx/html/dockerfile.png

# Expõe a porta 80 do NGINX
EXPOSE 80

# Executa o NGINX em primeiro plano (foreground)
CMD ["nginx", "-g", "daemon off;"]
