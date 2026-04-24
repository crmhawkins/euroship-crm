# Euroship CRM

CRM para Euroship: gestión de **Clientes → Barcos → Escalas → Pedidos → Pertrechos**.

## Stack
- Laravel 11 (PHP 8.3)
- Filament 3 (panel `/admin`)
- MySQL 8
- Docker + docker-compose (listo para Coolify)
- Bilingüe ES/EN

## Arranque rápido

```bash
cp .env.example .env
# editar DB_PASSWORD, DB_ROOT_PASSWORD, APP_URL

composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Abrir http://localhost:8000/admin

## Deploy con Docker / Coolify

```bash
# Variables requeridas: APP_KEY, DB_PASSWORD, DB_ROOT_PASSWORD
docker compose up -d --build
```

En Coolify: apuntar a este repo, asignar las variables de entorno y exponer puerto 80 del servicio `app`. El contenedor ejecuta `migrate --force` y `db:seed --force` en cada arranque (idempotente).

## Usuarios semilla

| Email                      | Password       |
| -------------------------- | -------------- |
| dani@hawkins.es            | Hawkins2025!   |
| juancarlos@euroship.es     | Euroship2025!  |

## Idioma

Cambio rápido: `/locale/es` o `/locale/en`. Se persiste en `users.locale`.

## Colores (branding Euroship)

- Primary `#293C8E`
- Secondary `#29A6DF`
- Accent `#F3903F`
- Texto `#333333`

## Reasignación de pedidos

Un pedido puede moverse a otra escala **del mismo barco**. La validación se aplica tanto en el form Filament como en el `rule()` del campo `escala_id`.
