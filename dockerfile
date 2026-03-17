FROM php:8.3.10-fpm

# Pour une image basée sur Debian (ex: php:8.2-fpm)
RUN docker-php-ext-install pdo pdo_mysql

# OU pour une image basée sur Alpine (ex: php:8.2-fpm-alpine)
RUN docker-php-ext-install pdo_mysql

RUN apt-get update && apt-get install -y \
    libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo pdo_mysql