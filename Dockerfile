FROM php:8.0-apache

# Enable Apache mod_rewrite if needed
RUN a2enmod rewrite

# Install dependencies for the PostgreSQL PDO extension
RUN apt-get update && apt-get install -y libpq-dev

# Now install the PostgreSQL PDO extension
RUN docker-php-ext-install pdo_pgsql

COPY . /var/www/html
WORKDIR /var/www/html

EXPOSE 80
