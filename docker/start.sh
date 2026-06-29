#!/usr/bin/env bash
set -e

export PORT="${PORT:-7860}"

sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf

rm -f bootstrap/cache/*.php

# ============================================
# Download gambar saat startup (gak disimpan di git)
# ============================================
mkdir -p public/images

if [ ! -f public/images/bg-login.webp ]; then
    echo "==> Download background image..."
    mkdir -p public/images
    curl -sLo /tmp/bg.jpg "https://dpmd.pasuruankab.go.id/storage/file_media/cc16405a863814d5402ea575f2e4d972.jpg" --max-time 20 --insecure 2>/dev/null || true
    if [ -s /tmp/bg.jpg ]; then
        php -r "
            \$img = @imagecreatefromjpeg('/tmp/bg.jpg');
            if (\$img) { imagewebp(\$img, 'public/images/bg-login.webp', 60); imagedestroy(\$img); }
        " 2>/dev/null || true
    fi
    rm -f /tmp/bg.jpg
fi

if [ ! -f public/images/logo-pasuruan.png ]; then
    echo "==> Download logo..."
    curl -sLo public/images/logo-pasuruan.png "https://upload.wikimedia.org/wikipedia/commons/9/9a/Lambang_Kabupaten_Pasuruan.png" --max-time 20 --insecure 2>/dev/null || true
fi

# ============================================
# Konfigurasi database — pilih otomatis
# ============================================
if [ -n "$DB_HOST" ]; then
    # Mode: PostgreSQL (dari env vars — pas buat HF Spaces + Supabase)
    echo "==> Mendeteksi DB_HOST, mengaktifkan PostgreSQL..."

    cat > .env <<EOF
APP_NAME="${APP_NAME:-Data Desa}"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-https://localhost}
DB_CONNECTION=pgsql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-postgres}
DB_USERNAME=${DB_USERNAME:-postgres}
DB_PASSWORD=${DB_PASSWORD}
DB_SSLMODE=${DB_SSLMODE:-require}
SESSION_DRIVER=${SESSION_DRIVER:-database}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}
SESSION_ENCRYPT=${SESSION_ENCRYPT:-false}
SESSION_SECURE_COOKIE=${SESSION_SECURE_COOKIE:-true}
SESSION_HTTP_ONLY=${SESSION_HTTP_ONLY:-true}
SESSION_SAME_SITE=${SESSION_SAME_SITE:-lax}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
CACHE_STORE=${CACHE_STORE:-file}
FILESYSTEM_DISK=${FILESYSTEM_DISK:-local}
DEFAULT_ADMIN_PASSWORD=${DEFAULT_ADMIN_PASSWORD:-}
EOF

else
    # Mode: SQLite (default, buat development / tanpa env vars)
    if [ ! -f .env ]; then
        echo "==> .env tidak ditemukan, membuat baru (SQLite)..."
        cat > .env <<'SQLLITE'
APP_NAME="Data Desa"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://localhost
DB_CONNECTION=sqlite
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
QUEUE_CONNECTION=database
CACHE_STORE=file
DEFAULT_ADMIN_PASSWORD=
SQLLITE
    fi

    # Init SQLite database file
    if [ ! -f database/database.sqlite ]; then
        echo "==> database.sqlite tidak ditemukan, membuat baru..."
        touch database/database.sqlite
        chown www-data:www-data database/database.sqlite
    fi
fi

# Generate APP_KEY jika belum ada
if ! grep -q "^APP_KEY=[A-Za-z0-9]" .env 2>/dev/null; then
    php artisan key:generate --ansi --force
fi

# Database migrations
php artisan migrate --force 2>/dev/null || true

# Seed data awal (hanya jika tabel kosong)
php artisan db:seed --force 2>/dev/null || true

# Setup Laravel
php artisan package:discover --ansi 2>/dev/null || true
php artisan storage:link 2>/dev/null || true

# Config cache
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

exec apache2-foreground
