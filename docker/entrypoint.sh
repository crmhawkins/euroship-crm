#!/usr/bin/env bash
set -e

cd /var/www/html

# Esperar a la base de datos MySQL.
if [ -n "${DB_HOST}" ]; then
    echo "Esperando a MySQL en ${DB_HOST}:${DB_PORT:-3306}..."
    for i in $(seq 1 60); do
        if php -r "try { new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); echo 'ok'; } catch(Exception \$e) { exit(1); }" 2>/dev/null | grep -q ok; then
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
php artisan filament:assets || true
php artisan filament:optimize || true

# Storage link para assets públicos.
php artisan storage:link || true

chown -R www-data:www-data storage bootstrap/cache || true

exec "$@"
