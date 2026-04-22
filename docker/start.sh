#!/usr/bin/env bash
set -e

cd /var/www/html

php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

apache2-foreground
