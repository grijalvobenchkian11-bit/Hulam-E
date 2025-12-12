# Set the base image for the PHP environment
FROM php:8.1-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo pdo_mysql \
    && apt-get clean

# Install Composer (PHP dependency manager)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory inside the container
WORKDIR /var/www

# Copy the application files into the container (from 'back/' folder to '/var/www' in container)
COPY ./back /var/www

# Verify that files, including composer.json, are copied correctly
RUN ls -la /var/www

# Install PHP dependencies using Composer
RUN composer install --no-dev --optimize-autoloader

# Expose port 80 to access the app
EXPOSE 80

# Run Laravel's development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
