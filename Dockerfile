FROM php:8.0-apache

# Enable Apache mod_rewrite if needed
RUN a2enmod rewrite

# Install PostgreSQL PDO extension (if it's not already installed)
RUN docker-php-ext-install pdo_pgsql

COPY . /var/www/html
WORKDIR /var/www/html

EXPOSE 80
