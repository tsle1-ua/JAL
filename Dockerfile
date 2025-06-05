FROM php:8.2-cli

# Install system dependencies
RUN apt-get update \
    && apt-get install -y git unzip libpng-dev libonig-dev libxml2-dev zip curl libsqlite3-dev \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www

RUN cp .env.example .env && composer install --no-interaction --prefer-dist --no-progress

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
