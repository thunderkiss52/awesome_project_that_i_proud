FROM composer:2 AS composer

FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY docker/backend/start-backend /usr/local/bin/start-backend
COPY docker/backend/start-worker /usr/local/bin/start-worker

RUN chmod +x /usr/local/bin/start-backend /usr/local/bin/start-worker

WORKDIR /var/www/html
