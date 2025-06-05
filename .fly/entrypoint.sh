#!/bin/sh

# .fly/entrypoint.sh

# Espera o banco de dados estar pronto, se necessário (boa prática)
# Adicione a lógica de "wait-for-it" aqui se encontrar problemas de conexão com o DB no deploy

# Executa as migrações e otimizações do Laravel
php /var/www/html/artisan migrate --force
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache
php /var/www/html/artisan view:cache

# Inicia o Supervisor, que vai gerenciar o Nginx e o PHP-FPM
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
