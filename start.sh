#!/bin/bash
set -e

echo "Starting deployment..."

# Set proper permissions
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Start PHP-FPM in background
echo "Starting PHP-FPM..."
php-fpm -D

# Wait for PHP-FPM to be ready
sleep 2

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Wait for database
echo "Waiting for database connection..."
sleep 5

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Seed database (skip if already seeded)
echo "Seeding database..."
php artisan db:seed --class=StudentSeeder --force || echo "Database already seeded or seed failed"

# Cache config
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache

echo "Starting Nginx..."
# Start Nginx in foreground
nginx -g "daemon off;"
