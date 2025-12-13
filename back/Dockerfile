FROM php:8.2-cli

WORKDIR /var/www


RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        mbstring \
        zip \
        xml \
        bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


COPY . .


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


RUN composer clear-cache

RUN composer install \
    --no-dev \
    --no-interaction \
    --ignore-platform-reqs

RUN chmod -R 775 storage bootstrap/cache


EXPOSE 10000


CMD php artisan serve --host=0.0.0.0 --port=10000
