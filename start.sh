#!/bin/bash

# Wait for database to be ready (if using PostgreSQL)
if [ ! -z "$DATABASE_URL" ] || [ ! -z "$DB_HOST" ]; then
    echo "Waiting for database connection..."
    php artisan migrate --force
fi

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Create storage link if it doesn't exist
if [ ! -L "public/storage" ]; then
    php artisan storage:link
fi

# Start the server
php artisan serve --host=0.0.0.0 --port=$PORT 