FROM php:7.2-fpm-alpine

RUN apk update
RUN apk add --no-cache postgresql-dev
RUN docker-php-ext-install pdo pdo_pgsql bcmath

#COPY ./ /var/www/analytics/backend

COPY php.ini /usr/local/etc/php

WORKDIR /var/www/analytics/backend