#!/bin/bash

# Exit immediately if a command exits with a non-zero status.
set -e

# Start the SSH service
echo "Starting SSH service..."
# Generate host keys if they don't exist
ssh-keygen -A
# Start SSH daemon in background
/usr/sbin/sshd -D &
echo "SSH service started on port 2222"

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
