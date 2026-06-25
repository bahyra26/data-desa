#!/usr/bin/env bash
set -e

export PORT="${PORT:-8080}"

sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf

rm -f bootstrap/cache/*.php
php artisan package:discover --ansi
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec apache2-foreground
