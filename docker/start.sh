#!/usr/bin/env bash
set -e

cd /var/www/html

PORT="${PORT:-8000}"

sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/\*:80/*:${PORT}/" /etc/apache2/sites-available/000-default.conf
sed -i "s/\${PORT}/${PORT}/g" /etc/apache2/sites-available/000-default.conf

php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  php artisan migrate --force
fi

if [ "${WARM_SNAPSHOTS:-false}" = "true" ]; then
  php artisan scraper:refresh-snapshots --path=home --path=anime-list --path=genres || true
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

apache2-foreground
