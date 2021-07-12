FROM php:7.4-buster

RUN docker-php-ext-install sockets
RUN pecl install redis-5.1.1

COPY docker/php.ini-production /tmp/php.ini-production

RUN mv "/tmp/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY dist/integracao /usr/bin
