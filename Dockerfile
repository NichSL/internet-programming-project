# ====================================================
# Stage 1: Build Frontend Assets with Node.js & Vite 8
# ====================================================
FROM node:22-alpine AS frontend-builder
WORKDIR /app

# Copy package manifests and npm configuration
COPY package.json package-lock.json .npmrc* ./

# Install ALL dependencies including devDependencies (required for Vite & Tailwind)
RUN npm ci --include=dev

# Copy source files required for compilation
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

# Build frontend production assets
RUN npm run build

# ====================================================
# Stage 2: Production PHP 8.4 Runtime (PHP-FPM + Nginx)
# ====================================================
FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

# Install system dependencies (Nginx, Supervisor, SQLite, ICU, Libzip, etc.)
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng \
    libjpeg-turbo \
    libzip \
    oniguruma \
    icu-libs \
    sqlite-libs

# Install required PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    bcmath \
    mbstring \
    xml \
    curl \
    zip \
    opcache \
    intl

# Copy official Composer binary
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Copy web server and PHP configurations
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy application source code (ignoring files specified in .dockerignore)
COPY . /var/www/html

# Copy compiled frontend assets from Stage 1
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

# Install PHP dependencies without dev packages
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction --prefer-dist \
    && php artisan package:discover --ansi \
    && rm -rf /root/.composer /usr/local/bin/composer

# Prepare storage and bootstrap cache directories
RUN mkdir -p /var/www/html/storage/framework/cache/data \
             /var/www/html/storage/framework/sessions \
             /var/www/html/storage/framework/views \
             /var/www/html/storage/logs \
             /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose default port (Railway injects $PORT at runtime)
EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

