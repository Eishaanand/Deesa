#!/usr/bin/env bash
set -e

cd /var/www/html

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ -f composer.json ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ -f package.json ] && [ ! -d node_modules ]; then
    npm install
fi

if [ -f artisan ]; then
    php artisan key:generate --force || true

    if [ "${DB_HOST}" != "" ]; then
        echo "Waiting for database at ${DB_HOST}:${DB_PORT:-3306}..."
        until mysqladmin ping \
            --skip-ssl \
            -h"${DB_HOST}" \
            -P"${DB_PORT:-3306}" \
            -u"${DB_USERNAME}" \
            -p"${DB_PASSWORD}" \
            --silent; do
            sleep 2
        done
    fi

    php artisan migrate --force || true
    php artisan db:seed --force || true
    php artisan storage:link || true
fi

exec "$@"
