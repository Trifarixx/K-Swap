# 1. On utilise une image avec un vrai serveur web inclus (Apache)
FROM php:8.2-apache

# 2. Configuration d'Apache pour pointer vers le dossier /public de Symfony
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

# 3. Installation des dépendances système et des extensions PHP (intl, pdo_mysql)
RUN apt-get update && apt-get install -y \
    libicu-dev \
    git \
    unzip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo pdo_mysql

# 4. Récupération de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Définition du dossier de travail
WORKDIR /var/www/html

# 6. Copie de tout votre code source dans le serveur
COPY . .

# 7. IMPORTANT : On installe les paquets PHP MAIS on bloque les scripts (cache) pendant le build
RUN composer install --no-interaction --optimize-autoloader --no-scripts

# 8. On donne les droits d'écriture à Apache pour le dossier var/ (cache et logs)
RUN chown -R www-data:www-data var/

# 9. Au démarrage du serveur (et non pendant le build), on vide le cache puis on lance Apache
CMD php bin/console cache:clear && apache2-foreground