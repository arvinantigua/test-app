FROM php:8.2-fpm-alpine

RUN addgroup -g 1000 laravel && adduser -G laravel -g laravel -s /bin/sh -D laravel

RUN mkdir -p /var/www/laravel

ADD . /var/www/laravel

RUN chmod -R 777 /var/www/laravel/storage

RUN chmod -R 777 /var/www/laravel/bootstrap/cache

RUN docker-php-ext-install pdo pdo_mysql

RUN chown -R laravel:laravel /var/www/laravel
