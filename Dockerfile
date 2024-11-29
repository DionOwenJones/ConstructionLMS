FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nginx \
    && rm -rf /var/lib/apt/lists/* # Clean up apt cache

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Configure PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    sed -i 's/memory_limit = 128M/memory_limit = 512M/g' "$PHP_INI_DIR/php.ini"

# Configure PHP-FPM
RUN sed -i 's/listen = 127.0.0.1:9000/listen = \/var\/run\/php-fpm.sock/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/;listen.owner = www-data/listen.owner = www-data/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/;listen.group = www-data/listen.group = www-data/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/;listen.mode = 0660/listen.mode = 0660/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/pm.max_children = 5/pm.max_children = 10/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/pm.start_servers = 2/pm.start_servers = 4/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/pm.min_spare_servers = 1/pm.min_spare_servers = 2/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/pm.max_spare_servers = 3/pm.max_spare_servers = 6/g' /usr/local/etc/php-fpm.d/www.conf && \
    echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "php_admin_value[error_log] = /dev/stderr" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "catch_workers_output = yes" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "decorate_workers_output = no" >> /usr/local/etc/php-fpm.d/www.conf


# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && npm install -g npm@latest \
    && rm -rf /var/lib/apt/lists/* # Clean up apt cache

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure Nginx
COPY nginx.conf /etc/nginx/nginx.conf
RUN rm -rf /etc/nginx/sites-enabled/default

# Set working directory
WORKDIR /var/www

# Copy composer files first to leverage Docker cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist

# Copy package.json files
COPY package.json package-lock.json ./
RUN npm ci --production && \
    npm cache clean --force # Clean npm cache

# Copy the rest of the application code
COPY . .

# Set NODE_ENV and build assets
ENV NODE_ENV=production
RUN npm run build || true && \
    rm -rf node_modules # Remove node_modules after build

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache \
    && chmod -R 755 /var/run \
    && rm -rf /var/www/storage/*.key

# Create environment file
COPY .env.example .env
RUN php artisan key:generate

# Expose port 80
EXPOSE 80

# Create start script
RUN echo '#!/bin/sh\n\
set -e\n\
echo "Creating PHP-FPM run directory..."\n\
mkdir -p /var/run/php-fpm\n\
\n\
echo "Running Laravel artisan commands..."\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
php artisan storage:link\n\
\n\
echo "Setting permissions..."\n\
chown -R www-data:www-data /var/run/php-fpm\n\
chmod 755 /var/run/php-fpm\n\
\n\
echo "Starting PHP-FPM..."\n\
php-fpm -D\n\
\n\
echo "Checking PHP-FPM socket..."\n\
timeout=30\n\
while [ ! -S /var/run/php-fpm.sock ] && [ $timeout -gt 0 ]; do\n\
    echo "Waiting for PHP-FPM socket... ($timeout seconds remaining)"\n\
    ls -la /var/run/php-fpm/\n\
    ls -la /var/run/\n\
    sleep 1\n\
    timeout=$((timeout-1))\n\
done\n\
\n\
if [ ! -S /var/run/php-fpm.sock ]; then\n\
    echo "Error: PHP-FPM socket not created after 30 seconds"\n\
    exit 1\n\
fi\n\
\n\
echo "PHP-FPM socket created successfully:"\n\
ls -la /var/run/php-fpm.sock\n\
\n\
echo "Starting Nginx..."\n\
exec nginx -g "daemon off;"' > /start.sh && chmod +x /start.sh

# Start servers
CMD ["/start.sh"]