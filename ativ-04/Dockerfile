# Estágio 1: Build, Instalação e Validações de Qualidade
FROM node:20-alpine AS build
WORKDIR /app

# Copia dependências e instala
COPY package*.json ./
RUN npm ci

# Copia o código-fonte e scripts de teste
COPY . .

# Executa a pipeline de validação (Linter, Cobertura Jest e Segurança)
RUN npm run lint
RUN npm run test:coverage
RUN npm run security:check

# Estágio 2: Imagem final otimizada para execução
FROM node:20-alpine
WORKDIR /app

# Copia dependências apenas de produção
COPY package*.json ./
RUN npm ci --only=production

# Copia os arquivos da aplicação compilada/validada
COPY --from=build /app/src ./src

# Porta exposta especificada pela atividade
EXPOSE 5000

CMD ["npm", "start"]
