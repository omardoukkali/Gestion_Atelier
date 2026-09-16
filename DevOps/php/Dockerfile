FROM php:8.2-fpm

# System deps for the PHP extensions we need
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer (needed to install Laravel's PHP deps inside the container)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www