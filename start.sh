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

# Cache config (do this before starting nginx)
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache

echo "Starting Nginx..."
# Start Nginx in foreground (non-blocking)
nginx -g "daemon off;" &

# Check if database is empty before seeding
STUDENT_COUNT=$(php artisan tinker --execute="echo \App\Models\Student::count();")
if [ "$STUDENT_COUNT" -eq "0" ]; then
    echo "Database is empty. Seeding database in background..."
    php artisan db:seed --class=StudentSeeder --force > /var/www/storage/logs/seed.log 2>&1 &
else
    echo "Database already has $STUDENT_COUNT records. Skipping seed."
fi

# Keep container alive
wait
