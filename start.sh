#!/bin/bash
set -e

echo "Starting deployment..."

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

echo "Starting PHP-FPM..."
php-fpm -D

sleep 2

if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

echo "Waiting for database connection..."
sleep 5

echo "Running migrations..."
php artisan migrate --force

echo "Caching configuration..."
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache

echo "Starting Nginx..."
nginx -g "daemon off;" &

STUDENT_COUNT=$(php artisan tinker --execute="echo \App\Models\Student::count();")
if [ "$STUDENT_COUNT" -eq "0" ]; then
    echo "Database is empty. Seeding database in background..."
    php artisan db:seed --class=StudentSeeder --force > /var/www/storage/logs/seed.log 2>&1 &
else
    echo "Database already has $STUDENT_COUNT records. Skipping seed."
fi

wait
