FROM php:8.3-apache AS php-base

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libicu-dev \
        libpng-dev \
        libpq-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install \
        bcmath \
        gd \
        intl \
        pdo_pgsql \
        zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

FROM node:24-bookworm-slim AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN npm run build

FROM php-base AS vendor

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

FROM php-base

WORKDIR /var/www/html

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/start.sh /usr/local/bin/start

RUN chmod +x /usr/local/bin/start \
    && chown -R www-data:www-data storage bootstrap/cache database

# Generate images during build (not stored in git — HF blocks binary files)
COPY docker/setup-images.php /tmp/setup-images.php
RUN php /tmp/setup-images.php && rm /tmp/setup-images.php

EXPOSE 7860

CMD ["start"]
