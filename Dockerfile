# --- Build Stage ---
FROM php:8.2-fpm-alpine AS builder

# 1. Install system dependencies + Build tools
# We need rrdtool-dev for the headers and autoconf/g++/make for PECL
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev \
    zip \
    unzip \
    nodejs \
    npm \
    rrdtool-dev \
    autoconf \
    g++ \
    make

# 2. Install Standard PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd

# 3. Install PECL RRD (The CFLAGS fix is mandatory here for PHP 8.2)
RUN export CFLAGS="$CFLAGS -Wno-incompatible-pointer-types" && \
    pecl install rrd && \
    docker-php-ext-enable rrd

# 4. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY composer.json composer.lock package.json package*.json ./

# 5. Install PHP & JS dependencies
RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader --no-scripts \
    && (npm ci --omit=dev || npm install --omit=dev)

COPY . .
RUN composer dump-autoload --optimize

# --- Production Stage ---
FROM php:8.2-fpm-alpine

# Install only runtime libraries (keeps the image small)
RUN apk add --no-cache \
    libpng \
    libzip \
    libjpeg-turbo \
    freetype \
    libpq \
    mysql-client \
    bash \
    rrdtool

# Copy compiled extensions and config from builder
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

WORKDIR /var/www
COPY --from=builder --chown=www-data:www-data /var/www /var/www

# Set permissions for Laravel
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
    storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000
USER www-data

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
