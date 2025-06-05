# Dockerfile
# Versão final com limpeza de cache e regeneração de manifesto de pacotes

ARG PHP_VERSION=8.2
ARG NODE_VERSION=20

# --- Estágio 1: Builder de PHP (Composer) ---
FROM composer:2 AS composer_builder
WORKDIR /app
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

RUN apt-get update && apt-get install -y --no-install-recommends \
    sudo git curl unzip libzip-dev libpng-dev libonig-dev libxml2-dev libpq-dev supervisor nginx \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql zip exif pcntl gd

WORKDIR /var/www/html

COPY . .
COPY --from=composer_builder /app/vendor/ /var/www/html/vendor/
COPY --from=node_builder /app/public/build/ /var/www/html/public/build/
COPY --from=node_builder /app/node_modules/ /var/www/html/node_modules/

COPY .fly/nginx/nginx.conf /etc/nginx/sites-available/default
COPY .fly/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY .fly/entrypoint.sh /usr/bin/entrypoint.sh
RUN chmod +x /usr/bin/entrypoint.sh

# --- CORREÇÃO APLICADA AQUI ---
# Limpa qualquer cache compilado e força a redescoberta de pacotes
# baseado apenas no que está na pasta 'vendor' de produção.
RUN php artisan clear-compiled
RUN php artisan package:discover --ansi

# Roda os outros comandos de otimização do Laravel
RUN php artisan storage:link
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8080

ENTRYPOINT ["/usr/bin/entrypoint.sh"]
