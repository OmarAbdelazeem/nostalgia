# Stage 1: Composer Build
# Install dependencies in a temporary, separate image to keep the final image clean.
FROM composer:2.7 as builder

WORKDIR /app

# Copy dependency definitions
COPY composer.json composer.lock ./

# Install production dependencies
RUN composer install --no-interaction --no-plugins --no-scripts --no-dev --prefer-dist --optimize-autoloader

# Copy the rest of the application source code
COPY . .

# Stage 2: Final Production Image
# Use the official PHP 8.2 Apache image as a base.
FROM php:8.2-apache

# Set the working directory
WORKDIR /var/www/html

# Set ServerName to suppress Apache warnings
RUN echo "ServerName nostalgia-api" >> /etc/apache2/apache2.conf

# Install required system packages and PHP extensions
# - libzip-dev, unzip: for zip extension
# - libicu-dev: for intl extension
RUN apt-get update && apt-get install -y \
    openssh-server \
    libzip-dev \
    unzip \
    libicu-dev \
    libsqlite3-dev \
    libonig-dev \
    && docker-php-ext-install -j$(nproc) \
    pdo_sqlite \
    pdo_mysql \
    mbstring \
    bcmath \
    zip \
    intl \
    opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure SSH
RUN mkdir -p /var/run/sshd \
    && echo "root:Docker!" | chpasswd \
    && sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config \
    && sed -i 's/#PasswordAuthentication yes/PasswordAuthentication yes/' /etc/ssh/sshd_config

# SSH port
EXPOSE 2222

# Copy custom opcache configuration
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Configure Apache to serve the Laravel public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN a2enmod rewrite

# Copy application code from the current directory
COPY . /var/www/html

# Copy installed dependencies from the builder stage
COPY --from=builder /app/vendor /var/www/html/vendor

# Set up permissions for Laravel storage and cache
# Also create the persistent data directory for SQLite
RUN mkdir -p /home/data \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /home/data \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy and set up the entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 80
EXPOSE 80

# Set the entrypoint
ENTRYPOINT ["entrypoint.sh"]

# Start Apache
CMD ["apache2-foreground"]
