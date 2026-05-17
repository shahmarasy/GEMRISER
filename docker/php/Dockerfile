FROM php:8.3-fpm-alpine

RUN apk add --no-cache git unzip libzip-dev oniguruma-dev icu-dev libpng-dev linux-headers

RUN docker-php-ext-install pdo_mysql bcmath intl gd zip mbstring

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www
