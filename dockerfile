FROM php:8.2-cli

WORKDIR /var/www

# Cài extension Laravel cần
RUN apt-get update && apt-get install -y \
    zip unzip curl git libxml2-dev libzip-dev libpng-dev libjpeg-dev libonig-dev \
    sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy source code
COPY --chown=www-data:www-data . /var/www

# Cấp quyền
RUN chmod -R 755 /var/www

# Install dependency (production)
RUN composer install --no-dev --optimize-autoloader

# Copy env template (Render sẽ override bằng biến môi trường thật)
COPY .env.example .env

# Expose port 8000 cho Render
EXPOSE 8000

# Start Laravel bằng artisan serve
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
