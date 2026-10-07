# ─────────────────────────────────────────────────────────────────
# Stage 1: Build / Dependency Stage (Version Pinned)
# ─────────────────────────────────────────────────────────────────
FROM composer:2.8.5 AS builder

WORKDIR /app

# Copy composer definition and lock file for cached layer installation
COPY composer.json composer.lock ./

# Install production dependencies only
RUN composer install \
    --no-interaction \
    --no-dev \
    --prefer-dist \
    --no-scripts \
    --no-autoloader

# Copy application source code
COPY . .

# Generate optimized production autoloader
RUN composer dump-autoload --optimize --no-dev

# ─────────────────────────────────────────────────────────────────
# Stage 2: Final Lightweight Runtime Image (Alpine Pinned)
# ─────────────────────────────────────────────────────────────────
FROM php:8.4-cli-alpine

# Install minimal runtime dependencies & PHP extensions
RUN apk add --no-cache \
        sqlite-dev \
        libzip-dev \
        curl \
        icu-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

WORKDIR /var/www/html

# Copy app files and optimized vendor from builder stage
COPY --from=builder /app /var/www/html

# Setup env, permissions, and database initialization
RUN if [ ! -f .env ]; then cp .env.example .env; fi \
    && mkdir -p storage/framework/sessions \
                storage/framework/views \
                storage/framework/cache \
                storage/logs \
                bootstrap/cache \
                database \
    && touch database/database.sqlite \
    && php artisan key:generate --force \
    && php artisan migrate --force \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache database

# Ensure container runs as non-root user (Requirement 4)
USER www-data

# Expose HTTP port
EXPOSE 8000

# Healthcheck configuration (Requirement 4)
HEALTHCHECK --interval=15s --timeout=5s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:8000/ || exit 1

# Start lightweight web server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]