#!/bin/bash

# Exit immediately if a command exits with a non-zero status.
set -e

# Start the SSH service
echo "Starting SSH service..."
/usr/sbin/sshd

# Run Laravel optimizations
echo "Running Laravel optimizations..."
php artisan config:cache
php artisan route:cache

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

echo "Starting Apache..."
# Execute the original command (apache2-foreground)
exec "$@"
