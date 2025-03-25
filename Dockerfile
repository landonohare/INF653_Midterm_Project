# Use the official PHP image with Apache
FROM php:8.0-apache

# Copy your project files to the container’s document root
COPY . /var/www/html/

# Optional: Enable Apache mod_rewrite if you need URL rewriting
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Expose port 80 for web traffic
EXPOSE 80
