#!/usr/bin/env sh
set -e

# Use Render-provided PORT or default
PORT=${PORT:-10000}
echo "[entrypoint] Using PORT=$PORT"

# Clear caches
php /var/www/html/artisan config:clear || true
# php /var/www/html/artisan cache:clear || true

# Only generate APP_KEY if not set in env
if [ -z "$APP_KEY" ]; then
    echo "[entrypoint] APP_KEY not set. Generating temporary key."
    php /var/www/html/artisan key:generate --force || true
fi

echo "[entrypoint] Starting Laravel built-in server"
exec php /var/www/html/artisan serve --host=0.0.0.0 --port=${PORT}
