FROM php:8.2-fpm

# Au lieu de : RUN composer install
RUN composer install --no-scripts --no-interaction

# Installation des dépendances système et des extensions PHP
RUN apt-get update && apt-get install -y \
    libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo pdo_mysql