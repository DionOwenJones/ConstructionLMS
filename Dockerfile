FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Configure PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    sed -i 's/memory_limit = 128M/memory_limit = 512M/g' "$PHP_INI_DIR/php.ini"

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files first to leverage Docker cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy package.json files
COPY package.json package-lock.json ./
RUN npm ci

# Copy the rest of the application code
COPY . .

# Set NODE_ENV and build assets
ENV NODE_ENV=production
RUN npm run build || true

# Configure Apache
RUN a2enmod rewrite headers
COPY apache.conf /etc/apache2/sites-available/000-default.conf
RUN ln -sf /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Create environment file
COPY .env.example .env
RUN php artisan key:generate

# Expose port 80
EXPOSE 80

# Start Apache with Laravel setup
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan storage:link && \
    apache2-foreground