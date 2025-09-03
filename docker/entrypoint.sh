#!/bin/bash

# Exit immediately if a command exits with a non-zero status.
set -e

# Run Laravel optimizations
echo "Running Laravel optimizations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache..."
# Execute the original command (apache2-foreground)
exec "$@"
