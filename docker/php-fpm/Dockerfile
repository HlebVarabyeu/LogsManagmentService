FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html