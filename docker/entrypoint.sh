#!/usr/bin/env bash
set -e

cd /var/www/html

# ── Ensure required directories exist (named volumes can wipe image contents) ──
mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    database
chown -R www-data:www-data storage bootstrap/cache database
chmod -R 775 storage bootstrap/cache database

# ── .env setup ──────────────────────────────────────────────
if [ ! -f ".env" ]; then
    echo "→ Creating .env from .env.example"
    cp .env.example .env
fi

# ── APP_KEY ─────────────────────────────────────────────────
if grep -q "^APP_KEY=$" .env || grep -q "^APP_KEY=\"\"$" .env; then
    echo "→ Generating APP_KEY"
    php artisan key:generate --force --no-interaction
fi

# ── SQLite database file ─────────────────────────────────────
DB_CONNECTION=$(grep -E "^DB_CONNECTION=" .env | cut -d '=' -f2 | tr -d '"')
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_DATABASE=$(grep -E "^DB_DATABASE=" .env | cut -d '=' -f2 | tr -d '"')
    DB_DATABASE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    if [ ! -f "${DB_DATABASE}" ]; then
        echo "→ Creating SQLite database at ${DB_DATABASE}"
        mkdir -p "$(dirname "${DB_DATABASE}")"
        touch "${DB_DATABASE}"
        chown www-data:www-data "${DB_DATABASE}"
    fi
fi

# ── Migrations ───────────────────────────────────────────────
echo "→ Running migrations"
php artisan migrate --force --no-interaction

# ── Storage link ─────────────────────────────────────────────
echo "→ Linking storage"
php artisan storage:link --no-interaction 2>/dev/null || true

# ── Optimize ─────────────────────────────────────────────────
echo "→ Optimizing application"
php artisan optimize --no-interaction

echo "→ Starting PHP-FPM"
exec "$@"
