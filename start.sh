#!/bin/bash

# Start PHP-FPM in background
php-fpm -D

# Wait for database
sleep 5

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --class=StudentSeeder --force || true

# Cache config
php artisan config:cache
php artisan route:cache

# Start Nginx in foreground
nginx -g "daemon off;"
