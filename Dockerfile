FROM php:8.2-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip pdo pdo_mysql

COPY .env.example .env
COPY . .

CMD php artisan serve --host=0.0.0.0 --port=8000
