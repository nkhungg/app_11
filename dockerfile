FROM php:8.2-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    zip unzip curl git libxml2-dev libzip-dev libpng-dev libjpeg-dev libonig-dev \
    sqlite3 libsqlite3-dev

RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files first and install dependencies
COPY composer.json composer.lock ./
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --no-interaction --prefer-dist --verbose

# Copy rest of the app
COPY --chown=www-data:www-data . /var/www

RUN chmod -R 755 /var/www
RUN chown -R www-data:www-data /var/www

# Ensure .env exists before generating key
RUN cp .env.example .env || true
RUN php artisan key:generate

ENV COMPOSER_CACHE_DIR=/tmp

EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000
