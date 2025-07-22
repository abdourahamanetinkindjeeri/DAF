# Utilise PHP avec FPM
FROM php:8.2-fpm

# Installe les dépendances nécessaires
RUN apt-get update \
    && apt-get install -y nginx libpq-dev supervisor zip unzip libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Installe Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copie le code de l'application
COPY . /var/www/html

COPY .env /var/www/html/.env


# Installe les dépendances PHP
WORKDIR /var/www/html
RUN composer install --no-interaction --prefer-dist

# Copie la config Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copie la config Supervisor
COPY docker/supervisord.conf /etc/supervisord.conf

# Donne les bons droits
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

# Lance Supervisor qui gère PHP-FPM et Nginx
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"] 