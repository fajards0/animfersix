#!/usr/bin/env bash
set -e

cd /var/www/html

PORT="${PORT:-8000}"

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

exec php artisan serve --host=0.0.0.0 --port="${PORT}"
