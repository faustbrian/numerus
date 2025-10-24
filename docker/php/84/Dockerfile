FROM php:8.4-cli
WORKDIR /usr/src/myapp

RUN apt-get update && apt-get install zip unzip git curl libicu-dev libgmp-dev -y \
    && docker-php-ext-install intl bcmath gmp \
    && pecl install pcov xdebug \
    && docker-php-ext-enable pcov xdebug \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    && mkdir /.composer && chmod 777 /.composer
