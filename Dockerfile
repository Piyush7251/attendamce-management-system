# Use official PHP image with Apache
FROM php:8.2-apache

# Install PDO MySQL extension for database connection
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite (optional, useful for clean routing)
RUN a2enmod rewrite

# Copy root redirect index.php
COPY ./index.php /var/www/html/index.php

# Copy attendanceapp directory to Apache web root (retaining /attendanceapp subdirectory structure)
COPY ./attendanceapp /var/www/html/attendanceapp

# Set appropriate permissions for Apache
RUN chown -R www-data:www-data /var/www/html/attendanceapp \
    && chmod -R 755 /var/www/html/attendanceapp

# Expose port 80
EXPOSE 80
