FROM php:8.2-cli


WORKDIR /var/www


RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    curl \
    && docker-php-ext-install pdo pdo_pgsql


COPY . .


RUN curl -sS https://getcomposer.org/installer | php \
    && php composer.phar install --no-dev --optimize-autoloader


EXPOSE 10000


CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000
