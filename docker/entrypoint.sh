#!/bin/sh
set -e

PORT="${PORT:-8080}"
echo "Configuring Nginx to listen on port ${PORT}..."
sed -i "s/__PORT__/${PORT}/g" /etc/nginx/http.d/default.conf

# Ensure critical directories exist
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache \
         /var/www/html/database

# If using SQLite and DB file doesn't exist, create it safely
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_FILE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    if [ ! -f "$DB_FILE" ]; then
        echo "Creating SQLite database file at $DB_FILE..."
        touch "$DB_FILE"
    fi
    chown www-data:www-data "$DB_FILE" 2>/dev/null || true
    chmod 664 "$DB_FILE" 2>/dev/null || true
fi

# Ensure storage and bootstrap cache permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Check APP_KEY
if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY environment variable is not set! Generating a temporary key for this container..."
    php artisan key:generate --force
fi

# Create storage symlink if missing
php artisan storage:link --no-interaction || true

# Run database migrations safely (NEVER fresh, NEVER seed)
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force --no-interaction || echo "WARNING: Database migration encountered an issue, continuing startup."
fi

# Cache configuration, routes, and views for optimal performance
echo "Caching Laravel configuration, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Application ready. Starting Supervisor on port ${PORT}..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

