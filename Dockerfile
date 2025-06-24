FROM php:8.2-apache

# Installer extensions nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql gd

# Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# Autoriser l’utilisation de .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copier le code de l’application
COPY . /var/www/html

# Changer les permissions pour Apache
RUN chown -R www-data:www-data /var/www/html
