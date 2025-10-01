# Use the official PHP image with Apache
FROM php:8.2-apache

# Install necessary extensions for MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache's rewrite module for clean URLs
RUN a2enmod rewrite

# Set the working directory in the container
WORKDIR /var/www/html

# Expose port 80 for the Apache server
EXPOSE 80