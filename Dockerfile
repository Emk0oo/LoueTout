# Utiliser une image officielle PHP avec Apache
FROM php:8.2-apache

# Installer des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_pgsql

# Installer Composer
# Télécharger et installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Copier les fichiers de l'application dans le conteneur
WORKDIR /var/www/html
COPY . .

# Installer les dépendances de Symfony
RUN composer install

# Changer les permissions du dossier de travail
RUN chown -R www-data:www-data /var/www/html

# Exposer le port 8000 pour le serveur Symfony
EXPOSE 8000

# Commande pour démarrer Symfony
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]