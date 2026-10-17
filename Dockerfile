# Stage 1: Build Composer vendor dependencies
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Stage 2: Production FrankenPHP environment
FROM dunglas/frankenphp:latest-php8.4

WORKDIR /app

# Copy Composer binary from official Composer image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install essential PHP extensions for Laravel and databases (PostgreSQL, MySQL, SQLite, Redis)
RUN install-php-extensions \
    pdo_pgsql \
    pdo_mysql \
    pdo_sqlite \
    bcmath \
    gd \
    intl \
    opcache \
    zip \
    pcntl \
    redis

# Configure production PHP ini
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy source code and vendor
COPY . /app
COPY --from=vendor /app/vendor /app/vendor

# Copy Caddy server configuration and entrypoint
COPY Caddyfile /etc/caddy/Caddyfile
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

# Run package discovery and autoload dump
RUN composer dump-autoload --optimize --no-dev

# Set permissions for Laravel directories
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Render default port
ENV PORT=10000

ENTRYPOINT ["docker-entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]

