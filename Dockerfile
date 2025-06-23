# Utilise l'image officielle PHP 8.2 avec Apache
FROM php:8.2-apache

# Active les modules rewrite et headers d'Apache
RUN a2enmod rewrite headers

# Modifie la configuration Apache pour autoriser AllowOverride All dans /var/www/html
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
