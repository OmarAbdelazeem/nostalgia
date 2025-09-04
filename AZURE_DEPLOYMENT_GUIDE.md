# Complete Azure App Service Deployment Guide for Laravel Projects

## Overview

This guide provides step-by-step instructions for deploying a Laravel application to Azure App Service using Docker containers. It includes all the problems we encountered and their solutions during the deployment process.

## Prerequisites

- Azure account with App Service access
- Docker Hub account
- GitHub repository
- Local development environment with:
  - PHP 8.2+
  - Composer
  - Node.js and npm
  - Git

## Table of Contents

1. [Project Setup](#project-setup)
2. [Docker Configuration](#docker-configuration)
3. [GitHub Actions CI/CD](#github-actions-cicd)
4. [Azure App Service Setup](#azure-app-service-setup)
5. [Common Issues and Solutions](#common-issues-and-solutions)
6. [Verification and Testing](#verification-and-testing)
7. [Maintenance and Updates](#maintenance-and-updates)

## Project Setup

### 1. Laravel Application Preparation

Ensure your Laravel project is production-ready:

```bash
# Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Generate application key
php artisan key:generate

# Set up environment variables
cp .env.example .env
```

### 2. Database Configuration

For Azure App Service, we recommend using SQLite for simplicity:

```php
// config/database.php
'default' => env('DB_CONNECTION', 'sqlite'),
'sqlite' => [
    'driver' => 'sqlite',
    'url' => env('DATABASE_URL'),
    'database' => env('DB_DATABASE', '/home/data/database.sqlite'),
    'prefix' => '',
    'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
],
```

### 3. CORS Configuration

Configure CORS to handle cross-origin requests:

```php
// config/cors.php
'paths' => ['*'],
'allowed_methods' => ['*'],
'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:4200')),
'allowed_origins_patterns' => [],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => false,
```

### 4. Production URL Configuration

Force HTTPS URLs in production to avoid mixed content issues:

```php
// app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\URL;

public function boot(): void
{
    // Force HTTPS URLs in production to avoid mixed content issues
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
    }
}
```

## Docker Configuration

### 1. Dockerfile

Create a multi-stage Dockerfile optimized for Azure App Service:

```dockerfile
# Stage 1: Composer Build
FROM composer:2.7 as builder

WORKDIR /app

# Copy dependency definitions
COPY composer.json composer.lock ./

# Install production dependencies
RUN composer install --no-interaction --no-plugins --no-scripts --no-dev --prefer-dist --optimize-autoloader

# Copy the rest of the application source code
COPY . .

# Stage 2: Final Production Image
FROM php:8.2-apache

# Set the working directory
WORKDIR /var/www/html

# Set ServerName to suppress Apache warnings
RUN echo "ServerName nostalgia-api" >> /etc/apache2/apache2.conf

# Install required system packages and PHP extensions
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

# Configure SSH for Azure App Service
RUN mkdir -p /var/run/sshd \
    && echo "root:Docker!" | chpasswd \
    && sed -i 's/^#\?Port .*/Port 2222/' /etc/ssh/sshd_config \
    && sed -i 's/^#\?PasswordAuthentication .*/PasswordAuthentication yes/' /etc/ssh/sshd_config \
    && sed -i 's/^#\?PermitRootLogin .*/PermitRootLogin yes/' /etc/ssh/sshd_config \
    && sed -i 's/^#\?UsePAM .*/UsePAM yes/' /etc/ssh/sshd_config

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
```

### 2. Entrypoint Script

Create `docker/entrypoint.sh` for container startup:

```bash
#!/usr/bin/env bash
set -euxo pipefail

# Ensure SSH host keys exist (idempotent)
echo "Generating SSH host keys..."
ssh-keygen -A

# Start sshd with logs to stderr; listen on 2222
echo "Starting SSH daemon on port 2222..."
/usr/sbin/sshd -D -e -p 2222 &

# Small wait, then print diagnostics (will appear in Container Logs)
sleep 2
echo "=== SSH Diagnostics ==="
( command -v ss >/dev/null && ss -lntp || netstat -tlnp ) || true
pgrep -fal sshd || true
echo "=== End SSH Diagnostics ==="

# Run Laravel optimizations (don't fail startup if they fail)
echo "Running Laravel optimizations..."
php artisan config:cache || true
php artisan route:cache || true
php artisan migrate --force || true

echo "Starting Apache..."
# Hand off to Apache (or whatever "$@" is)
exec "$@"
```

### 3. PHP Opcache Configuration

Create `docker/php/opcache.ini`:

```ini
[opcache]
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

## GitHub Actions CI/CD

### 1. Workflow Configuration

Create `.github/workflows/azure-dockerhub-deploy.yml`:

```yaml
name: Build and Push to Docker Hub

on:
  push:
    branches: [ azure_deploy ]

jobs:
  build-and-push:
    runs-on: ubuntu-latest
    
    steps:
    - name: Checkout code
      uses: actions/checkout@v4
      
    - name: Set up Docker Buildx
      uses: docker/setup-buildx-action@v3
      
    - name: Log in to Docker Hub
      uses: docker/login-action@v3
      with:
        username: ${{ secrets.DOCKERHUB_USERNAME }}
        password: ${{ secrets.DOCKERHUB_TOKEN }}
        
    - name: Build and push Docker image
      uses: docker/build-push-action@v5
      with:
        context: .
        push: true
        tags: |
          omarabdelazeem/nostalgia:latest
        cache-from: type=gha
        cache-to: type=gha,mode=max
        
    # Optional: Deploy to Azure App Service
    # Uncomment and configure when ready
    # - name: Deploy to Azure App Service
    #   uses: azure/webapps-deploy@v3
    #   with:
    #     app-name: 'nostalgia-api'
    #     publish-profile: ${{ secrets.AZURE_WEBAPP_PUBLISH_PROFILE }}
```

### 2. Required GitHub Secrets

Add these secrets to your GitHub repository:

- `DOCKERHUB_USERNAME`: Your Docker Hub username
- `DOCKERHUB_TOKEN`: Docker Hub access token with read/write permissions

## Azure App Service Setup

### 1. Create App Service

1. Go to Azure Portal
2. Create a new App Service
3. Choose:
   - **Runtime**: Linux
   - **Pricing Tier**: Free F1 (or higher)
   - **Region**: Choose your preferred region

### 2. Configure Container Deployment

1. Go to **Deployment Center**
2. Select **Containers** tab
3. Choose **Other container registries**
4. Enter:
   - **Registry**: `Docker Hub`
   - **Image**: `yourusername/yourproject:latest`
   - **Tag**: `latest`
5. **Important**: Leave **Startup command** field **EMPTY**

### 3. Application Settings

Configure these environment variables in **Configuration** → **Application settings**:

```
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-generated-key
APP_URL=https://your-app-name.azurewebsites.net
DB_CONNECTION=sqlite
DB_DATABASE=/home/data/database.sqlite
CORS_ALLOWED_ORIGINS=https://your-app-name.azurewebsites.net
WEBSITES_PORT=80
WEBSITES_CONTAINER_START_TIME_LIMIT=600
WEBSITES_ENABLE_APP_SERVICE_STORAGE=true
```

### 4. Enable Always On (Recommended)

1. Go to **Configuration** → **General settings**
2. Enable **Always On**
3. Click **Save**

## Common Issues and Solutions

### Issue 1: Mixed Content Errors

**Problem**: Browser blocks HTTP resources on HTTPS pages
**Solution**: Force HTTPS URLs in production

```php
// app/Providers/AppServiceProvider.php
if ($this->app->environment('production')) {
    URL::forceScheme('https');
}
```

### Issue 2: SSH Connection Fails

**Problem**: "SSH CONN CLOSE" error in Azure web SSH
**Solution**: Proper SSH configuration and startup command

1. Ensure SSH daemon starts on port 2222
2. Use `UsePAM yes` in SSH config
3. **Critical**: Leave "Startup command" empty in Azure

### Issue 3: Container Crashes on Startup

**Problem**: Container exits immediately after starting
**Solution**: Robust entrypoint script

```bash
# Use || true for non-critical commands
php artisan config:cache || true
php artisan route:cache || true
php artisan migrate --force || true
```

### Issue 4: Database Seeding Not Working

**Problem**: Users not created during container startup
**Solution**: Run seeding manually via SSH

```bash
# SSH into container
cd /var/www/html
php artisan db:seed
```

### Issue 5: CORS Policy Errors

**Problem**: API requests blocked by CORS
**Solution**: Configure CORS properly

```php
// config/cors.php
'paths' => ['*'],
'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:4200')),
```

### Issue 6: Docker Build Failures

**Problem**: Missing PHP extensions during build
**Solution**: Install required system packages

```dockerfile
RUN apt-get update && apt-get install -y \
    libzip-dev \
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
    opcache
```

## Verification and Testing

### 1. Check Application Status

1. Visit your app URL: `https://your-app-name.azurewebsites.net`
2. Verify API endpoints work
3. Check Swagger documentation loads

### 2. Test SSH Access

1. Go to Azure Portal → Your App Service
2. Click **SSH** in the top menu
3. Verify you can run commands:
   ```bash
   cd /var/www/html
   php artisan --version
   ```

### 3. Verify Database

```bash
# Check database file exists
ls -la /home/data/database.sqlite

# Check tables
php artisan tinker --execute="print_r(DB::select('SELECT name FROM sqlite_master WHERE type=\"table\"'));"

# Check users
php artisan tinker --execute="echo 'Users: ' . App\Models\User::count();"
```

## Maintenance and Updates

### 1. Deploy Updates

1. Make changes to your code
2. Commit and push to `azure_deploy` branch
3. GitHub Actions will build and push new Docker image
4. Azure will automatically pull the latest image

### 2. Manual Container Updates

1. Go to **Deployment Center**
2. Click **Refresh** to pull latest image
3. Or restart the App Service

### 3. Database Management

```bash
# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### 4. Monitoring

- **Log Stream**: Monitor real-time logs
- **Metrics**: Check CPU, memory usage
- **Health Checks**: Verify application health

## Troubleshooting Commands

### Container Diagnostics

```bash
# Check running processes
ps aux

# Check listening ports
netstat -tlnp

# Check disk usage
df -h

# Check memory usage
free -h
```

### Laravel Diagnostics

```bash
# Check Laravel configuration
php artisan config:show

# Check routes
php artisan route:list

# Check database connection
php artisan tinker --execute="DB::connection()->getPdo();"
```

## Best Practices

1. **Always test locally** before deploying
2. **Use environment variables** for configuration
3. **Keep startup command empty** in Azure
4. **Monitor logs** regularly
5. **Use proper error handling** in entrypoint scripts
6. **Backup database** regularly
7. **Use HTTPS** everywhere
8. **Keep dependencies updated**

## Security Considerations

1. **Never commit** `.env` files
2. **Use strong passwords** for SSH
3. **Enable HTTPS** only
4. **Regular security updates**
5. **Monitor access logs**
6. **Use least privilege** principles

## Cost Optimization

1. **Use Free F1 tier** for development
2. **Enable Always On** only when needed
3. **Monitor resource usage**
4. **Scale up** only when necessary
5. **Use Azure credits** for testing

## Conclusion

This guide covers the complete deployment process for Laravel applications on Azure App Service. The key to successful deployment is:

1. Proper Docker configuration
2. Correct Azure settings
3. Robust error handling
4. Regular monitoring
5. Understanding common issues

Remember to always test changes in a development environment before deploying to production.

---

**Last Updated**: September 2025
**Version**: 1.0
**Author**: Based on real deployment experience
