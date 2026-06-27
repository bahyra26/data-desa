#!/usr/bin/env bash
set -e

export PORT="${PORT:-8080}"
export CACHE_STORE="${CACHE_STORE:-file}"

sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf

rm -f bootstrap/cache/*.php
php artisan package:discover --ansi
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Bersihkan session expired dari database biar query session tetap cepat
php artisan session:gc || true

exec apache2-foreground
