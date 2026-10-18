#!/usr/bin/env bash
set -e

# If SQLite is used, ensure the database file exists
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    mkdir -p /app/database
    touch /app/database/database.sqlite
fi

# Ensure storage directories exist and have proper permissions
mkdir -p /app/storage/framework/{sessions,views,cache} /app/storage/logs /app/bootstrap/cache
chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/database 2>/dev/null || true
chmod -R 775 /app/storage /app/bootstrap/cache 2>/dev/null || true

# Run database migrations and seed essential store data (graceful fallback if DB is not reachable immediately)
php artisan migrate --force || echo "Notice: Migrations could not run automatically at startup."
php artisan db:seed --force || echo "Notice: Database seed could not run automatically at startup."

# Optimize caches for production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start web server
exec "$@"

