# Dockerfile
FROM php:8.1-fpm

RUN apt-get update -y \
    && apt-get install -y --no-install-recommends \
        wget \
        zip \
        unzip \
        libzip-dev \
    && docker-php-ext-install zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN echo "xdebug.mode=coverage" > ${PHP_INI_DIR}/conf.d/xdebug.ini

# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app
ADD . /app/

RUN composer install
