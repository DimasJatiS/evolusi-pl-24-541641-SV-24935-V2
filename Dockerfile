# ─────────────────────────────────────────────────────────────────
# Stage 1 – Download Vendor Dependencies Only
# ─────────────────────────────────────────────────────────────────
FROM composer:2.8 AS vendor

WORKDIR /app

# CACHE LAYER: Hanya copy manifest composer
COPY composer.json composer.lock ./

RUN composer install \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

# ─────────────────────────────────────────────────────────────────
# Stage 2 – Final Runtime Image
# ─────────────────────────────────────────────────────────────────
FROM php:8.4-apache

# 1. Install sistem paket
RUN apt-get update && apt-get install -y --no-install-recommends \
        libsqlite3-dev \
        libzip-dev \
        unzip \
        curl \
    && docker-php-ext-install pdo pdo_sqlite zip \
    && rm -rf /var/lib/apt/lists/*

# 2. Copy Composer CLI ke image runtime
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# 3. Konfigurasi Apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri \
        -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/000-default.conf \
        /etc/apache2/apache2.conf \
    && a2enmod rewrite

WORKDIR /var/www/html

# 4. Siapkan struktur direktori & permission DULUAN (Layer ini di-cache!)
RUN mkdir -p storage/framework/{sessions,views,cache} \
             storage/logs \
             bootstrap/cache \
             database \
    && touch database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache

# 5. Copy HANYA folder vendor dari Stage 1 (Di-cache selama composer.json tidak berubah)
COPY --from=vendor /app/vendor /var/www/html/vendor

# 6. Copy kode aplikasi (HANYA layer ini ke bawah yang diproses ulang saat kode berubah)
COPY . .
COPY .env.example .env

# Set Permission
RUN mkdir -p storage/framework/{sessions,views,cache} \
             storage/logs \
             bootstrap/cache \
             database \
    && touch database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache

# 7. Autoload & Migration cepat
RUN composer dump-autoload --optimize \
    && php artisan key:generate --force \
    && php artisan migrate --force

EXPOSE 80