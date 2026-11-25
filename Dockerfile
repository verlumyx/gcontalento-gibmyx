# =============================================================================
# Stage 1: Build PHP dependencies + frontend assets
# =============================================================================
FROM php:8.5-fpm-alpine AS builder

RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev \
    libzip-dev \
    nodejs \
    npm

RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install \
    gd \
    pdo \
    mbstring \
    xml \
    bcmath \
    zip \
    pcntl \
    intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies first (layer caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist

# Install Node dependencies (layer caching)
COPY package.json package-lock.json* ./
RUN npm install

# Copy application source
COPY . .

# Create a temporary .env with SQLite so artisan can boot during the asset build
RUN mkdir -p bootstrap/cache storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && php -r "echo 'APP_KEY=base64:' . base64_encode(random_bytes(32)) . PHP_EOL;" > .env \
    && printf "APP_ENV=local\nDB_CONNECTION=sqlite\nDB_DATABASE=/tmp/build.db\n" >> .env \
    && touch /tmp/build.db \
    && php artisan package:discover --ansi \
    && npm run build \
    && rm -f /tmp/build.db

# =============================================================================
# Stage 2: Development runtime (PHP-FPM + Node + Composer, full dev deps)
# Used by docker-compose for local development. Has every tool the workflow
# needs (artisan, tests, vite/npm with PHP available) so commands
# run *inside* this container via `docker compose exec app ...`.
# =============================================================================
FROM php:8.5-fpm-alpine AS development

RUN apk add --no-cache \
    git \
    curl \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libxml2-dev \
    oniguruma-dev \
    icu-dev \
    libzip-dev \
    nodejs \
    npm \
    shadow

# Adjust www-data user to match host user UID/GID (1000)
ARG UID=1000
ARG GID=1000
RUN usermod -u ${UID} www-data 2>/dev/null || true \
    && groupmod -g ${GID} www-data 2>/dev/null || true

RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install \
    gd \
    pdo \
    mbstring \
    xml \
    bcmath \
    zip \
    pcntl \
    intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Seed vendor/ (WITH dev dependencies) and node_modules into the image so the
# anonymous volumes in docker-compose start populated. The source itself is
# bind-mounted at runtime.
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-scripts --prefer-dist
COPY package.json package-lock.json* ./
RUN npm install

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/entrypoint.sh"]

# =============================================================================
# Stage 3: PHP-FPM production runtime
# =============================================================================
FROM php:8.5-fpm-alpine AS production

RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libxml2-dev \
    oniguruma-dev \
    icu-dev \
    libzip-dev

# Note: Zend OPcache is already compiled into the php:8.5 base image,
# so it must not be passed to docker-php-ext-install. It is configured
# via docker/php/opcache.ini below.
RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install \
    gd \
    pdo \
    mbstring \
    xml \
    bcmath \
    zip \
    pcntl \
    intl

WORKDIR /var/www/html

# Copy built application
COPY --from=builder /var/www/html .

# Remove build-time artifacts
RUN rm -rf .env node_modules docker Dockerfile docker-compose.yml .dockerignore Makefile

# Backup public dir for shared volume initialization
RUN cp -rp public /var/www/public-init

# PHP configuration
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/entrypoint.sh"]
