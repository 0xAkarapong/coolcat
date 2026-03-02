# ============================================================
# Stage 1 – PHP dependencies (composer)
# ============================================================
FROM composer:2 AS composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --optimize-autoloader \
    --prefer-dist

# ============================================================
# Stage 2 – Frontend assets (bun)
# ============================================================
FROM oven/bun:1-alpine AS node

WORKDIR /app

COPY package.json bun.lock* ./
RUN bun install --frozen-lockfile

COPY vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

# flux.css is imported from vendor; copy it in so Vite can resolve it
COPY --from=composer /app/vendor/livewire/flux/dist/flux.css ./vendor/livewire/flux/dist/flux.css

RUN bun run build

# ============================================================
# Stage 3 – Production image (PHP 8.4 FPM + Alpine)
# ============================================================
FROM php:8.5-fpm-alpine AS production

# System dependencies
RUN apk add --no-cache \
    bash \
    sqlite \
    sqlite-libs \
    libzip \
    libpng \
    libjpeg-turbo \
    freetype \
    icu-libs \
    oniguruma \
    && apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    sqlite-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
    pdo \
    pdo_sqlite \
    mbstring \
    pcntl \
    bcmath \
    zip \
    gd \
    intl \
    && apk del .build-deps \
    && rm -rf /tmp/*

WORKDIR /var/www/html

# Copy PHP vendor dependencies from composer stage
COPY --from=composer /app/vendor ./vendor

# Copy application source
COPY . .

# Copy compiled frontend assets from node stage
COPY --from=node /app/public/build ./public/build

# Ensure all required Laravel directories exist (some are excluded by .dockerignore)
RUN mkdir -p bootstrap/cache \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy and enable entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
