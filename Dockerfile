FROM php:apache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install --yes \
    git libpq-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www

COPY composer.json composer.json
RUN composer install --no-cache --no-interaction --no-dev --optimize-autoloader

COPY html html
COPY src src
