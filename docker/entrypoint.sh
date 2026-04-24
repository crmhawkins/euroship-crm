#!/usr/bin/env bash
set -e

cd /var/www/html

# Esperar a la base de datos MySQL.
if [ -n "${DB_HOST}" ]; then
    echo "Esperando a MySQL en ${DB_HOST}:${DB_PORT:-3306}..."
    for i in {1..60}; do
        if mysqladmin ping -h"${DB_HOST}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent; then
            echo "MySQL listo."
            break
        fi
        sleep 2
    done
fi

# Generar APP_KEY si no existe.
if [ -z "${APP_KEY}" ] || [ "${APP_KEY}" = "" ]; then
    if [ -f .env ]; then
        php artisan key:generate --force || true
    fi
fi

# Migraciones + seed idempotente.
php artisan migrate --force || true
php artisan db:seed --force || true

# Cachés de producción.
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan filament:optimize || true

# Storage link para assets públicos.
php artisan storage:link || true

chown -R www-data:www-data storage bootstrap/cache || true

exec "$@"
