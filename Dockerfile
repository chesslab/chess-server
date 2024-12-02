FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    libzip-dev \
    unzip \
    zip \
    libpng-dev \
    stockfish

RUN docker-php-ext-install mysqli pdo_mysql gd

RUN docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-install \
        pcntl

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /usr/share/chess-server

COPY composer.json composer.json

COPY composer.lock composer.lock

RUN composer install \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --no-dev \
    --prefer-dist

# By default, Composer runs as root inside the container.
# This can lead to permission issues on your host filesystem.

RUN chown -R 1000:1000 vendor

RUN chmod -R 775 vendor
