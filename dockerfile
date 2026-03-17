FROM php:8.2-fpm

# 1. Installation des dépendances système, utilitaires (git, unzip) et extensions PHP
RUN apt-get update && apt-get install -y \
    libicu-dev \
    git \
    unzip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo pdo_mysql

# 2. Récupération de Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Définition du dossier de travail
WORKDIR /var/www/html

# 4. Copie des fichiers de votre projet dans le conteneur
COPY . .

# 5. Installation des dépendances PHP (maintenant que les extensions sont présentes)
RUN composer install --no-interaction --optimize-autoloader