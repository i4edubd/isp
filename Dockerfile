# Build stage
FROM php:8.2-fpm-alpine AS builder

# Install system dependencies
RUN apk add --no-cache \
    git curl libpng-dev libzip-dev libjpeg-turbo-dev \
    freetype-dev postgresql-dev zip unzip nodejs npm \
    rrdtool-dev rrdtool

# Install PHP extensions (Added pdo_pgsql)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd

# Install RRD PHP extension
RUN pecl install rrd && docker-php-ext-enable rrd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY composer.json composer.lock package.json package-lock.json ./
RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader --no-scripts && npm ci --omit=dev

COPY . .
RUN composer dump-autoload --optimize

# Production stage
FROM php:8.2-fpm-alpine

# Install runtime dependencies (Added libpq for pdo_pgsql)
RUN apk add --no-cache \
    libpng libzip libjpeg-turbo freetype libpq \
    mysql-client nodejs npm bash rrdtool

# Copy extensions and config from builder
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

WORKDIR /var/www
COPY --from=builder --chown=www-data:www-data /var/www /var/www

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
    storage/logs storage/app/rrd storage/app/graphs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000
USER www-data

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
