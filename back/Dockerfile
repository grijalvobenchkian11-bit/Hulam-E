FROM php:8.2-cli

WORKDIR /var/www

# System deps + PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
 && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    xml \
    bcmath \
 && rm -rf /var/lib/apt/lists/*

# Copy app
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && rm composer-setup.php

# Install PHP deps (prefer respecting composer.lock; avoid ignore-platform-reqs unless you must)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Render sets $PORT. Default to 10000 locally.
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
