#!/usr/bin/env bash
set -e

export PORT="${PORT:-8080}"
export CACHE_STORE="${CACHE_STORE:-file}"

sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf

rm -f bootstrap/cache/*.php

# Buat .env kalo belum ada (HF Spaces docker build gak include file yg di .gitignore)
if [ ! -f .env ]; then
    echo "==> .env tidak ditemukan, membuat dari template..."
    cp .env.example .env
    # Override dengan konfigurasi yang cocok untuk HF Spaces (SQLite)
    sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
    sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" .env
    sed -i "s|APP_URL=.*|APP_URL=https://bahyra26-data-desa.hf.space|" .env
    sed -i "s/DB_CONNECTION=.*/DB_CONNECTION=sqlite/" .env
    sed -i "/^DB_HOST/d; /^DB_PORT/d; /^DB_DATABASE=/d; /^DB_USERNAME=/d; /^DB_PASSWORD=/d; /^DB_SSLMODE=/d" .env
    sed -i "s/SESSION_ENCRYPT=.*/SESSION_ENCRYPT=false/" .env
    sed -i "s/CACHE_STORE=.*/CACHE_STORE=file/" .env
    sed -i "s/DEFAULT_ADMIN_PASSWORD=.*/DEFAULT_ADMIN_PASSWORD=AdminDesa2026!/" .env
    php artisan key:generate --ansi --force
fi

# Init database kalo belum ada — migrasi + seed data awal
if [ ! -f database/database.sqlite ]; then
    echo "==> database.sqlite tidak ditemukan, membuat baru..."
    touch database/database.sqlite
    chown www-data:www-data database/database.sqlite
    php artisan migrate --force
    php artisan db:seed --force
fi

php artisan package:discover --ansi
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec apache2-foreground
