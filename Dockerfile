FROM php:8.3-fpm-bookworm AS base

# Dependencias del sistema
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    cron \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring xml zip gd bcmath opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Node.js para build de assets
FROM node:20-slim AS node-builder
WORKDIR /build
COPY package.json package-lock.json* ./
RUN npm ci
COPY vite.config.* tailwind.config.* postcss.config.* ./
COPY resources/ resources/
RUN npm run build

# Imagen final
FROM base AS production

WORKDIR /var/www/html

# Copiar código fuente
COPY . .

# Copiar assets compilados desde el builder
COPY --from=node-builder /build/public/build public/build/

# Instalar dependencias PHP (sin dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# PHP config para producción
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
COPY docker/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# Nginx config
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Cron para Laravel Scheduler
COPY docker/crontab /etc/cron.d/laravel-scheduler
RUN chmod 0644 /etc/cron.d/laravel-scheduler && crontab /etc/cron.d/laravel-scheduler

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Script de inicio
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
