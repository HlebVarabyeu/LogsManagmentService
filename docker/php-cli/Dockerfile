FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    cron \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY log_to_parse.log /var/tmp/log_to_parse.log

WORKDIR /var/www/html