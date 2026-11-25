#!/bin/sh
set -e

# -------------------------------------------------------
# Initialize shared public volume on first boot (if production)
# -------------------------------------------------------
if [ ! -f /var/www/html/public/index.php ] && [ -d /var/www/public-init ]; then
    echo "Initializing public directory..."
    cp -rp /var/www/public-init/. /var/www/html/public/
    chown -R www-data:www-data /var/www/html/public
    echo "Public directory initialized."
fi

# -------------------------------------------------------
# Ensure .env exists
# -------------------------------------------------------
if [ ! -f /var/www/html/.env ] && [ -f /var/www/html/.env.example ]; then
    echo "Creating .env from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# -------------------------------------------------------
# SQLite Database initialization
# -------------------------------------------------------
mkdir -p /var/www/html/database
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "Creating SQLite database..."
    touch /var/www/html/database/database.sqlite
fi
chmod 775 /var/www/html/database 2>/dev/null || true
chmod 664 /var/www/html/database/database.sqlite 2>/dev/null || true

# -------------------------------------------------------
# Ensure storage directory structure exists
# -------------------------------------------------------
mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs
if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data storage bootstrap/cache /var/www/html/database
fi
find storage bootstrap/cache -type d -exec chmod 775 {} + 2>/dev/null || true
find storage bootstrap/cache -type f -exec chmod 664 {} + 2>/dev/null || true

# Ensure vendor and node_modules are owned by www-data and writable
if [ "$(id -u)" = "0" ]; then
    WWW_UID=$(id -u www-data)
    if [ -d /var/www/html/vendor ] && [ "$(stat -c '%u' /var/www/html/vendor)" != "$WWW_UID" ]; then
        chown -R www-data:www-data /var/www/html/vendor 2>/dev/null || true
        chmod -R u=rwX,g=rwX /var/www/html/vendor 2>/dev/null || true
    fi
    if [ -d /var/www/html/node_modules ] && [ "$(stat -c '%u' /var/www/html/node_modules)" != "$WWW_UID" ]; then
        chown -R www-data:www-data /var/www/html/node_modules 2>/dev/null || true
        chmod -R u=rwX,g=rwX /var/www/html/node_modules 2>/dev/null || true
    fi
fi

# -------------------------------------------------------
# Generate app key if not set
# -------------------------------------------------------
if [ -z "$APP_KEY" ] && ! grep -qE '^APP_KEY=.+' /var/www/html/.env 2>/dev/null; then
    echo "Generating application key..."
    php artisan key:generate --ansi --force
fi

# -------------------------------------------------------
# Regenerate the package manifest so it always matches the
# dependencies actually installed in this container (avoids
# stale dev/no-dev provider mismatches in bootstrap/cache).
# -------------------------------------------------------
php artisan package:discover --ansi

# -------------------------------------------------------
# Run migrations
# -------------------------------------------------------
echo "Running migrations..."
php artisan migrate --force --no-interaction

# -------------------------------------------------------
# Cache config/routes in production
# -------------------------------------------------------
if [ "${APP_ENV}" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# -------------------------------------------------------
# Start PHP-FPM
# -------------------------------------------------------
echo "Starting PHP-FPM..."
exec php-fpm
