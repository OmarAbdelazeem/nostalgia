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
php artisan view:cache

echo "Starting Apache..."
# Execute the original command (apache2-foreground)
exec "$@"
