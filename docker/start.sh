#!/usr/bin/env bash
set -e

export PORT="${PORT:-8080}"

sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf

rm -f bootstrap/cache/*.php

# Hanya generate .env jika benar-benar tidak ada dan tidak ada env vars Zeabur
if [ ! -f .env ] && [ -z "$ZEABUR_SERVICE_NAME" ]; then
    echo "==> .env tidak ditemukan, membuat dari template..."
    if [ -f .env.example ]; then
        cp .env.example .env
        php artisan key:generate --ansi --force
    fi
fi

# Init database SQLite hanya jika pake SQLite dan env vars dari platform tidak ada
if [ ! -f database/database.sqlite ] && { [ ! -f .env ] || grep -q "DB_CONNECTION=sqlite" .env 2>/dev/null; } && [ -z "$DB_HOST" ]; then
    echo "==> database.sqlite tidak ditemukan, membuat baru..."
    touch database/database.sqlite
    chown www-data:www-data database/database.sqlite
fi

# Generate APP_KEY jika belum ada (penting untuk Zeabur & platform cloud)
if [ -f .env ] && grep -q "^APP_KEY=$" .env 2>/dev/null; then
    php artisan key:generate --ansi --force
elif [ ! -f .env ] && [ -z "$APP_KEY" ]; then
    php artisan key:generate --ansi --force 2>/dev/null || true
fi

# Jalankan migrasi otomatis jika database tersedia
php artisan migrate --force 2>/dev/null || true

# Seed hanya jika tabel masih kosong
php artisan db:seed --force 2>/dev/null || true

php artisan package:discover --ansi 2>/dev/null || true
php artisan storage:link 2>/dev/null || true

# Config cache dilewati jika APP_KEY belum di-set
if [ -n "$APP_KEY" ] || grep -q "^APP_KEY=" .env 2>/dev/null; then
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
    php artisan view:cache 2>/dev/null || true
fi

exec apache2-foreground
