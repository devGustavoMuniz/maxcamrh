# Dockerfile
# Versão corrigida com a ordem de cópia de arquivos ajustada no estágio do Composer

ARG PHP_VERSION=8.2
ARG NODE_VERSION=20

# --- Estágio 1: Builder de PHP (Composer) ---
FROM composer:2 AS composer_builder
WORKDIR /app
# CORREÇÃO: Copia todos os arquivos da aplicação ANTES de rodar o install.
# Isso garante que o script 'artisan' esteja disponível para o post-install.
COPY . .
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# --- Estágio 2: Builder de Frontend (Node.js) ---
FROM node:${NODE_VERSION}-bullseye-slim AS node_builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
COPY --from=composer_builder /app/vendor/ /app/vendor/
RUN npm run build
RUN npm prune --omit=dev

# --- Estágio 3: Imagem Final de Produção ---
FROM php:8.2-fpm-bullseye

# Instala dependências do sistema
RUN apt-get update && apt-get install -y --no-install-recommends \
    sudo git curl unzip libzip-dev libpng-dev libonig-dev libxml2-dev libpq-dev supervisor nginx \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala as extensões PHP
RUN docker-php-ext-install pdo pdo_pgsql zip exif pcntl gd

WORKDIR /var/www/html

# Copia os arquivos da aplicação e dependências dos estágios de build
COPY . .
COPY --from=composer_builder /app/vendor/ /var/www/html/vendor/
COPY --from=node_builder /app/public/build/ /var/www/html/public/build/
COPY --from=node_builder /app/node_modules/ /var/www/html/node_modules/

# Copia as configurações do servidor e entrypoint
COPY .fly/nginx/nginx.conf /etc/nginx/sites-available/default
COPY .fly/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY .fly/entrypoint.sh /usr/bin/entrypoint.sh
RUN chmod +x /usr/bin/entrypoint.sh

# Roda os comandos de otimização do Laravel
RUN php artisan storage:link
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Ajusta permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8080

ENTRYPOINT ["/usr/bin/entrypoint.sh"]
