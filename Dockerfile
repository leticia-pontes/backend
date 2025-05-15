FROM php:8.4-fpm-alpine

# Instalar as dependências e as extensões necessárias
RUN apk update && apk add --no-cache \
    oniguruma-dev \
    libzip-dev \
    zip \
    curl \
    git \
    && docker-php-ext-install pdo_mysql zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-interaction

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
