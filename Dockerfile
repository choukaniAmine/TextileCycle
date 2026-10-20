# Stage 1: Install PHP dependencies
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
COPY database/ database/

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-reqs

# Stage 2: Build frontend assets
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json vite.config.js ./
COPY resources/ resources/
COPY public/ public/

RUN npm ci && npm run build

# Stage 3: Production runtime
FROM php:8.3-apache AS production

WORKDIR /var/www/html

# Install system libraries and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libxml2-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        bcmath \
        opcache \
        intl \
        zip \
        gd \
    && rm -rf /var/lib/apt/lists/*

# Configure Apache for Laravel (port 8080 + AllowOverride All for .htaccess)
RUN a2enmod rewrite \
    && sed -i 's/Listen 80/Listen 8080/g' /etc/apache2/ports.conf

# Write a clean VirtualHost config with correct DocumentRoot and AllowOverride
RUN printf '<VirtualHost *:8080>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
        Options -Indexes +FollowSymLinks\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>\n' > /etc/apache2/sites-available/000-default.conf

# Copy application
COPY . .

# Copy production dependencies and compiled assets
COPY --from=vendor /app/vendor/ ./vendor/
COPY --from=frontend /app/public/build/ ./public/build/

# Generate Laravel package manifest
RUN php artisan package:discover --ansi

# Ensure Laravel writable directories exist
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Production PHP configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

EXPOSE 8080
