# --------------------------
# 1) Build frontend assets with Node
# --------------------------
FROM node:20-alpine AS node-build
WORKDIR /app

# Copy package files and install dependencies
COPY package*.json ./
RUN npm ci --no-audit --no-fund

# Copy rest of the code
COPY . ./

# Prepare .env for frontend build (Vite/Tailwind)
RUN cp .env.example .env || true \
 && sed -i "s|APP_URL=.*|APP_URL=https://part-timer-web.onrender.com|" .env

# Build frontend assets
RUN npm run build

# --------------------------
# 2) PHP runtime image
# --------------------------
FROM php:8.4-cli-bullseye

ENV APP_ENV=production
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zlib1g-dev libpng-dev libjpeg-dev \
    libfreetype6-dev libonig-dev libxml2-dev libicu-dev curl \
  && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-freetype || true
RUN docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl bcmath gd xml intl || true

# Install Redis
RUN pecl install redis || true && docker-php-ext-enable redis || true

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application code
COPY . /var/www/html

# Copy frontend assets from node build stage
COPY --from=node-build /app/public /var/www/html/public

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist || true

# Ensure permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

# Default port (Render will set $PORT); expose for clarity
EXPOSE 10000

# Minimal entrypoint will run the built-in server for demo purposes
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
